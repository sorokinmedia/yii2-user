<?php
namespace sorokinmedia\user\entities\UserMeta;

use sorokinmedia\user\entities\User\AbstractUser;
use sorokinmedia\user\entities\User\UserInterface;
use yii\db\ActiveRecord;

/**
 * Class AbstractUserMeta
 * @package sorokinmedia\user\entities\UserMeta
 */
abstract class AbstractUserMeta extends ActiveRecord implements UserInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_meta';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'notification_phone', 'notification_telegram',], 'integer'],
            [['full_name', 'display_name', 'tz', 'location', 'about'], 'string'],
            [['notification_email'], 'email'],
            [['tz'], 'string', 'max' => 100],
            [['tz'], 'default', 'value' => 'Europe/Moscow'],
            [['location'], 'string', 'max' => 250],
            [['notification_email', 'full_name', 'display_name'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => AbstractUser::class, 'targetAttribute' => ['user_id' => 'id']],
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
            'notification_email' => \Yii::t('app', 'E-mail для уведомлений'),
            'notification_phone' => \Yii::t('app', 'Телефон для уведомлений'),
            'notification_telegram' => \Yii::t('app', 'Telegram для уведомлений'),
            'full_name' => \Yii::t('app', 'Полное имя'),
            'display_name' => \Yii::t('app', 'Отображаемое имя'),
            'tz' => \Yii::t('app', 'Часовой пояс'),
            'location' => \Yii::t('app', 'Страна/Город'),
            'about' => \Yii::t('app', 'О себе'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne($this->__userClass, ['id' => 'user_id']);
    }
}