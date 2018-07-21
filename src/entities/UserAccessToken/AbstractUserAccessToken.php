<?php
namespace ma3obblu\user\entities\UserAccessToken;

use ma3obblu\user\entities\User\AbstractUser;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * Class UserAccessTokenAR
 * @package ma3obblu\user\entities\UserAccessToken
 *
 * @property int $user_id
 * @property string $access_token
 * @property int $created_at
 * @property int $updated_at
 * @property int $expired_at
 * @property int $is_active
 */
abstract class AbstractUserAccessToken extends ActiveRecord implements UserAccessTokenInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_access_token';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'access_token', 'expired_at'], 'required'],
            [['user_id', 'created_at', 'updated_at', 'expired_at', 'is_active'], 'integer'],
            [['access_token'], 'string', 'max' => 32],
            [['is_active'], 'default', 'value' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => \Yii::t('app', 'Пользователь'),
            'access_token' => \Yii::t('app', 'Токен доступа'),
            'created_at' => \Yii::t('app', 'Создан'),
            'updated_at' => \Yii::t('app', 'Изменен'),
            'expired_at' => \Yii::t('app', 'Срок действия'),
            'is_active' => \Yii::t('app', 'Активен'),
        ];
    }

    /**
     * генерирует токен из строки
     * @param string $string
     * @return string
     */
    public static function generateToken(string $string) : string
    {
        return md5($string . uniqid());
    }

    /**
     * @param bool $remember
     * @return int
     */
    public static function generateExpired(bool $remember) : int
    {
        if ($remember === true){
            return time() + (60 * 60 * 24 * 30); // 30 дней
        }
        return time() + (60 * 60 * 24); // 1 день
    }

    /**
     * деактивация токена
     * @return bool
     * @throws Exception
     */
    public function deactivate() : bool
    {
        $this->is_active = 0;
        $this->expired_at = time();
        if (!$this->save()){
            throw new Exception(\Yii::t('app', 'Ошибка при деактивации токена'));
        }
        return true;
    }

    /**
     * статический конструктор
     * @param AbstractUser $user
     * @param bool $remember
     * @return UserAccessTokenInterface
     * @throws Exception
     */
    abstract public static function create(AbstractUser $user, bool $remember = false) : UserAccessTokenInterface;
}