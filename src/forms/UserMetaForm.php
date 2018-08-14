<?php
namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\UserMeta\AbstractUserMeta;
use sorokinmedia\user\entities\UserMeta\UserMetaInterface;
use yii\base\Model;

/**
 * Class UserMetaForm
 * @package sorokinmedia\user\forms
 *
 * @property string $notification_email
 * @property string $notification_phone
 * @property string $avatar
 * @property string $full_name
 * @property string $tz
 * @property string $location
 * @property string $about
 */
class UserMetaForm extends Model
{
    public $notification_email;
    public $notification_tel;
    public $avatar;
    public $full_name;
    public $tz;
    public $location;
    public $about;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['notification_phone', ], 'integer'],
            [['full_name', 'tz', 'location', 'about'], 'string'],
            [['notification_email'], 'email'],
            [['tz'], 'string', 'max' => 100],
            [['tz'], 'default', 'value' => 'Europe/Moscow'],
            [['location'], 'string', 'max' => 250],
            [['notification_email', 'full_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'notification_email' => \Yii::t('app', 'E-mail для уведомлений'),
            'notification_phone' => \Yii::t('app', 'Телефон для уведомлений'),
            'full_name' => \Yii::t('app', 'Полное имя'),
            'tz' => \Yii::t('app', 'Часовой пояс'),
            'location' => \Yii::t('app', 'Страна/Город'),
            'about' => \Yii::t('app', 'О себе'),
        ];
    }

    /**
     * UserMetaForm constructor.
     * @param array $config
     * @param UserMetaInterface|null $userMeta
     */
    public function __construct(array $config = [], UserMetaInterface $userMeta = null)
    {
        if (!is_null($userMeta)){
            /** @var AbstractUserMeta $userMeta */
            $this->notification_email = $userMeta->notification_email;
            $this->notification_phone = $userMeta->notification_phone;
            $this->full_name = $userMeta->full_name;
            $this->tz = $userMeta->tz;
            $this->location = $userMeta->location;
            $this->about = $userMeta->about;
        }
        parent::__construct($config);
    }
}