<?php

namespace sorokinmedia\user\entities\SmsCode;

use sorokinmedia\ar_relations\RelationInterface;
use sorokinmedia\helpers\DateHelper;
use Throwable;
use sorokinmedia\user\entities\{User\AbstractUser, UserMeta\json\UserMetaPhone};
use sorokinmedia\user\forms\SmsCodeForm;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * This is the model class for table "sms_code".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $phone
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
 * @property SmsCodeForm $form
 */
abstract class AbstractSmsCode extends ActiveRecord implements RelationInterface, SmsCodeInterface
{
    public const MAX_PER_DAY = 3;
    public const MAX_PER_IP = 7;

    public $form;

    /**
     * AbstractSmsCode constructor.
     * @param array $config
     * @param SmsCodeForm|null $form
     */
    public function __construct(array $config = [], SmsCodeForm $form = null)
    {
        if ($form !== null) {
            $this->form = $form;
        }
        parent::__construct($config);
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'sms_code';
    }

    /**
     * получает последний код заданного типа для пользователя
     * @param AbstractUser $user
     * @param $type_id
     * @return null|ActiveRecord
     */
    public static function getCodeByUser(AbstractUser $user, int $type_id): ?ActiveRecord
    {
        return self::find()->where(['user_id' => $user->id, 'type_id' => $type_id])->orderBy(['id' => SORT_DESC])->one();
    }

    /**
     * получает последний код заданного типа по IP
     * @param $ip
     * @param $type_id
     * @return null|ActiveRecord
     */
    public static function getCodeByIp(string $ip, int $type_id): ?ActiveRecord
    {
        return self::find()->where(['ip' => $ip, 'type_id' => $type_id])->orderBy(['id' => SORT_DESC])->one();
    }

    /**
     * Сколько сегодня было запросов SMS с этого ip
     * Опционально : тип запроса смс
     * @param string $ip
     * @param int $type_id
     * @return int
     */
    public static function getRequestedTodayByIp(string $ip, int $type_id): int
    {
        return (int)self::find()
            ->where([
                'ip' => $ip,
                'is_validated' => 0,
                'is_deleted' => 0,
            ])
            ->andWhere(['between', 'created_at', time() - DateHelper::TIME_DAY_ONE, time()])
            ->andWhere(['type_id' => $type_id])
            ->count();
    }

    /**
     * Сколько сегодня было запросов SMS от этого пользователя
     * Опционально : тип запроса смс
     * @param AbstractUser $user
     * @param int $type_id
     * @return int
     */
    public static function getRequestedTodayByUser(AbstractUser $user, int $type_id): int
    {
        return (int)self::find()
            ->where([
                'user_id' => $user->id,
                'is_validated' => 0,
                'is_deleted' => 0,
            ])
            ->andWhere(['between', 'created_at', time() - DateHelper::TIME_DAY_ONE, time()])
            ->andWhere(['type_id' => $type_id])
            ->count();
    }

    /**
     * получает все коды отправленные юзеру сегодня. для сброса лимитов.
     * @param AbstractUser $user
     * @return array|ActiveRecord[]
     */
    public static function getRequestedTodayForUser(AbstractUser $user): array
    {
        return self::find()
            ->where([
                'user_id' => $user->id,
                'is_deleted' => 0,
            ])
            ->andWhere(['between', 'created_at', time() - DateHelper::TIME_DAY_ONE, time()])
            ->all();
    }

    /**
     * сбрасывает лимит у юзера
     * @param AbstractUser $user
     * @return bool
     * @throws \Exception
     */
    public static function resetLimit(AbstractUser $user): bool
    {
        $query = self::find()
            ->where([
                'user_id' => $user->id,
                'is_deleted' => 0,
            ]);
        $sms_codes = $query->all();
        foreach ($sms_codes as $sms_code) {
            /** @var $sms_code AbstractSmsCode */
            $sms_code->deleteModel();
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
        $this->getFromForm();
        $this->is_deleted = true;
        if (!$this->save()) {
            throw new \RuntimeException(Yii::t('app-sm-user', 'Ошибка при удалении кода'));
        }
        return true;
    }

    /**
     * форматтер для телефона (только +7)
     * @param UserMetaPhone $userMetaPhone
     * @return string
     */
    public static function phoneFormatter(UserMetaPhone $userMetaPhone): string
    {
        $phone = $userMetaPhone->country . $userMetaPhone->number;
        if (strlen($phone) === 11) {
            return '+' .$phone[0] . '(' . substr($phone, 1, 3) . ')' . substr($phone, 4, 3) . '-' . substr($phone, 7, 2) . '-' . substr($phone, 9);
        }
        return $phone;
    }

    /**
     * @return array
     */
    public function behaviors(): array
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
     * @return array
     */
    public function rules(): array
    {
        return [
            [['user_id', 'type_id', 'is_used', 'code'], 'integer'],
            [['ip'], 'ip'],
            [['phone'], 'string', 'max' => 12],
            [['is_validated', 'is_deleted'], 'boolean'],
            [['is_used'], 'default', 'value' => 0],
            [['is_validated', 'is_deleted'], 'default', 'value' => false],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app-sm-user', 'ID'),
            'user_id' => Yii::t('app-sm-user', 'Пользователь'),
            'phone' => Yii::t('app-sm-user', 'Номер телефона'),
            'created_at' => Yii::t('app-sm-user', 'Дата'),
            'code' => Yii::t('app-sm-user', 'Код'),
            'type_id' => Yii::t('app-sm-user', 'Тип'),
            'ip' => Yii::t('app-sm-user', 'IP'),
            'is_used' => Yii::t('app-sm-user', 'Кол-во использований'),
            'is_validated' => Yii::t('app-sm-user', 'Проверен'),
            'is_deleted' => Yii::t('app-sm-user', 'Удален'),
        ];
    }

    /**
     * добавление модели в БД
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    public function insertModel(): bool
    {
        $this->getFromForm();
        if (!$this->insert()) {
            throw new Exception(Yii::t('app-sm-user', $this->getMessage()));
        }
        return true;
    }

    /**
     * перенсти данные из формы в модель
     */
    public function getFromForm(): void
    {
        if ($this->form !== null) {
            $this->user_id = $this->form->user_id;
            $this->phone = $this->form->phone;
            $this->code = $this->form->code;
            $this->type_id = $this->form->type_id;
            $this->ip = $this->form->ip;
            $this->is_used = $this->form->is_used;
            $this->is_validated = $this->form->is_validated;
            $this->is_deleted = $this->form->is_deleted;
        }
    }

    /**
     * требует реализации в наслудуемом классе
     * сформировать сообщение исходя их типа кода
     * @return string
     */
    abstract public function getMessage(): string;

    /**
     * обновление модели
     * @return bool
     * @throws Exception
     */
    public function updateModel(): bool
    {
        $this->getFromForm();
        if (!$this->save()) {
            throw new Exception(Yii::t('app-sm-user', 'Ошибка при обновлении в БД'));
        }
        return true;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return static::getTypes($this->type_id);
    }

    /**
     * требует реализации в наследуемом классе
     * @param int|null $type_id
     * @return array|mixed
     */
    abstract public static function getTypes(int $type_id = null);

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne($this->__userClass, ['id' => 'user_id']);
    }

    /**
     * требует реализации в наследуемом классе
     * генерирует 4х значный код из цифр
     * @return int
     */
    abstract public function generateCode(): int;

    /**
     * отправка смс с кодом
     */
    abstract public function sendCode(): bool;

    /**
     * отметить как использованный
     * @param bool $is_validated
     * @return bool
     * @throws Exception
     */
    public function checkUse(bool $is_validated = false): bool
    {
        ++$this->is_used;
        $this->is_validated = $is_validated;
        if (!$this->save()) {
            throw new Exception(Yii::t('app-sm-user', 'Ошибка при сохранении статуса кода'));
        }
        return true;
    }
}
