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
 * @property array $full_name
 * @property string $tz
 * @property string $location
 * @property string $about
 * @property array $custom_fields
 */
class UserMetaForm extends Model
{
    public $notification_email;
    public $full_name;
    public $tz;
    public $location;
    public $about;
    public $custom_fields;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tz', 'location', 'about'], 'string'],
            [['notification_email'], 'email'],
            [['tz'], 'string', 'max' => 100],
            [['tz'], 'default', 'value' => 'Europe/Moscow'],
            [['location'], 'string', 'max' => 250],
            [['notification_email'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'notification_email' => \Yii::t('app', 'E-mail для уведомлений'),
            'full_name' => \Yii::t('app', 'Полное имя'),
            'tz' => \Yii::t('app', 'Часовой пояс'),
            'location' => \Yii::t('app', 'Страна/Город'),
            'about' => \Yii::t('app', 'О себе'),
            'custom_fields' => \Yii::t('app', 'Дополнительные данные'),
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
            $this->full_name = $userMeta->full_name;
            $this->tz = $userMeta->tz;
            $this->location = $userMeta->location;
            $this->about = $userMeta->about;
            $this->custom_fields = $userMeta->custom_fields;
        }
        parent::__construct($config);
    }
}