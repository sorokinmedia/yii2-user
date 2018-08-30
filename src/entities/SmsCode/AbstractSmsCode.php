<?php
namespace sorokinmedia\user\entities\SmsCode;

use sorokinmedia\ar_relations\RelationInterface;
use sorokinmedia\helpers\DateHelper;
use sorokinmedia\user\entities\{
    User\AbstractUser,UserMeta\json\UserMetaPhone
};
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "sms_code".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $phone
 * @property string $created_at
 * @property string $code
 * @property integer $type_id
 * @property string $ip
 * @property integer $is_used
 * @property boolean $is_validated
 * @property boolean $is_deleted
 *
 * @property AbstractUser $user
 * @property string $type
 */
abstract class SmsCode extends ActiveRecord implements RelationInterface, SmsCodeInterface
{
    const TYPE_VERIFY = 1; // подтверждение телефона
    const TYPE_RESTORE = 2; // восстановление пароля

    const MAX_PER_DAY = 3;
    const MAX_PER_IP = 7;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sms_code';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'phone', 'type_id', 'is_used'], 'integer'],
            [['created_at'], 'safe'],
            [['code'], 'string', 'max' => 10],
            [['ip'], 'ip'],
            [['is_validated', 'is_deleted'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'user_id' => \Yii::t('app', 'Пользователь'),
            'phone' => \Yii::t('app', 'Номер телефона'),
            'create_date' => \Yii::t('app', 'Дата'),
            'code' => \Yii::t('app', 'Код'),
            'type_id' => \Yii::t('app', 'Тип'),
            'ip' => \Yii::t('app', 'IP'),
            'is_used' => \Yii::t('app', 'Кол-во использований'),
            'is_validated' => \Yii::t('app', 'Проверен'),
            'is_deleted' => \Yii::t('app', 'Удален'),
        ];
    }

    /**
     * @param int|null $type_id
     * @return array|mixed
     */
    public static function getTypes(int $type_id = null)
    {
        $types = [
            self::TYPE_VERIFY => \Yii::t('app', 'Подтверждение номера телефона'),
            self::TYPE_RESTORE => \Yii::t('app', 'Восстановление пароля')
        ];
        if (!is_null($type_id)){
            return $types[$type_id];
        }
        return $types;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return self::getTypes($this->type_id);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne($this->__userClass, ['id' => 'user_id']);
    }

    /**
     * генерирует 4х значный код из цифр
     * @return int
     */
    public function generateCode() : int
    {
        return rand(1000, 9999);
    }

    /**
     * Добаляет новый код
     * @param AbstractUser|null $user
     * @param integer $phone
     * @param $type
     * @param string|null $ip
     * @return bool
     * @throws \Exception
     */
    public function addCode(AbstractUser $user = null, $phone, int $type_id, string $ip = '')
    {
        $this->code = $this->generateCode();
        $this->user_id = ($user) ? $user->id : null;
        $this->phone = $phone;
        $this->type_id = $type_id;
        $this->ip = $ip;
        if (!$this->save()) {
            throw new \Exception(\Yii::t('app', 'Ошибка при добавлении кода в БД'));
        }
        $this->sendCode();
        return true;
    }

    /**
     * отправка смс с кодом
     */
    public function sendCode()
    {
        $sms = new Sms();
        switch ($this->type_id) {
            case self::TYPE_WALLET_WITHDRAW:
            case self::TYPE_REG_V2:
            case self::TYPE_RESTORE_V2:
                $message = "Код подтверждения: {$this->code}";
                break;
            default:
                $message = "Код подтверждения: {$this->code}";
        }
        $sms->sendWithoutLog($this->phone, $message);
    }

    /**
     * получает последний код заданного типа для пользователя
     * @param AbstractUser $user
     * @param $type_id
     * @return null|\yii\db\ActiveRecord
     */
    public static function getCodeByUser(AbstractUser $user, int $type_id)
    {
        return self::find()->where(['user_id' => $user->id, 'type_id' => $type_id])->orderBy(['id' => SORT_DESC])->one();
    }

    /**
     * получает последний код заданного типа по IP
     * @param $ip
     * @param $type_id
     * @return null|\yii\db\ActiveRecord
     */
    public static function getCodeByIp(string $ip, int $type_id)
    {
        return self::find()->where(['ip' => $ip, 'type_id' => $type_id])->orderBy(['id' => SORT_DESC])->one();
    }

    /**
     * Сколько сегодня было запросов SMS с этого ip
     * Опционально : тип запроса смс
     * @param string $ip
     * @param int $type_id
     * @return int|string
     */
    public static function getRequestedTodayByIp(string $ip, int $type_id)
    {
        $query = self::find()
            ->where([
                'ip' => $ip,
                'is_validated' => 'false',
                'is_deleted' => 'false',
            ])
            ->andWhere(['between', 'created_at', time() - DateHelper::TIME_DAY_ONE, time()])
            ->andWhere(['type_id' => $type_id]);
        return $query->count();
    }

    /**
     * Сколько сегодня было запросов SMS от этого пользователя
     * Опционально : тип запроса смс
     * @param AbstractUser $user
     * @param int $type_id
     * @return int|string
     */
    public static function getRequestedTodayByUser(AbstractUser $user, int $type_id)
    {
        $query = self::find()
            ->where([
                'user_id' => $user->id,
                'is_validated' => 'false',
                'is_deleted' => 'false',
            ])
            ->andWhere(['between', 'created_at', time() - DateHelper::TIME_DAY_ONE, time()])
            ->andWhere(['type_id' => $type_id]);
        return $query->count();
    }

    /**
     * получает все коды отправленные юзеру сегодня. для сброса лимитов.
     * @param AbstractUser $user
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getRequestedTodayForUser(AbstractUser $user)
    {
        $query = self::find()
            ->where([
                'user_id' => $user->id,
                'is_deleted' => 'false',
            ])
            ->andWhere(['between', 'created_at', time() - DateHelper::TIME_DAY_ONE, time()]);
        return $query->all();
    }

    /**
     * сбрасывает лимит у юзера
     * @param AbstractUser $user
     * @return bool
     * @throws \Exception
     */
    public static function resetCodes(AbstractUser $user): bool
    {
        $query = self::find()
            ->where([
                'user_id' => $user->id,
                'is_deleted' => false,
            ]);
        $sms_codes = $query->all();
        foreach ($sms_codes as $sms_code) {
            /** @var $sms_code SmsCode */
            $sms_code->deleteModel();
        }
        return true;
    }

    /**
     * отметить как использованный
     * @param bool $is_validated
     * @return bool
     * @throws \Exception
     */
    public function checkUse($is_validated = false): bool
    {
        $this->is_used = $this->is_used + 1;
        $this->is_validated = $is_validated;
        if (!$this->save()) {
            throw new \Exception(\Yii::t('app', 'Ошибка при сохранении статуса кода'));
        }
        return true;
    }

    /**
     * пометить как удаленный. при сбросе лимитов
     * @return bool
     * @throws \Exception
     */
    public function deleteModel(): bool
    {
        $this->is_deleted = true;
        if (!$this->save()) {
            throw new \Exception(\Yii::t('app', 'Ошибка при удалении кода'));
        }
        return true;
    }

    /**
     * форматтер для телефона (только +7)
     * @param UserMetaPhone $userMetaPhone
     * @return string
     */
    public static function formatPhone(UserMetaPhone $userMetaPhone) : string
    {
        $phone = $userMetaPhone->country . $userMetaPhone->number;
        if (strlen($phone) == 11) {
            return "+" . substr($phone, 0, 1) . "(" . substr($phone, 1, 3) . ")" . substr($phone, 4, 3) . "-" . substr($phone, 7, 2) . "-" . substr($phone, 9);
        }
        return $phone;
    }
}
