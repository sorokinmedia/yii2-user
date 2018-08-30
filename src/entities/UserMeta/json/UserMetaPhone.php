<?php
namespace sorokinmedia\user\entities\UserMeta\json;

use yii\base\Model;

/**
 * Class UserMetaPhone
 * @package sorokinmedia\user\entities\UserMeta\json
 *
 * @property int $country
 * @property int $number
 */
class UserMetaPhone extends Model
{
    public $country;
    public $number;

    /**
     * UserMetaPhone constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country', 'number'], 'required'],
            [['country'], 'in', 'range' => [7]],
            [['number'], 'match', 'pattern' => '/^\d{10}$/'],
            [['number'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => \Yii::t('app', 'Пользователь'),
            'notification_email' => \Yii::t('app', 'E-mail для уведомлений'),
            'notification_phone' => \Yii::t('app', 'Телефон для уведомлений'),
            'notification_telegram' => \Yii::t('app', 'Telegram для уведомлений'),
            'full_name' => \Yii::t('app', 'Полное имя'),
            'tz' => \Yii::t('app', 'Часовой пояс'),
            'location' => \Yii::t('app', 'Страна/Город'),
            'about' => \Yii::t('app', 'О себе'),
            'custom_fields' => \Yii::t('app', 'Дополнительные данные')
        ];
    }
}