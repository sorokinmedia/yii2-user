<?php
namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\UserMeta\json\UserMetaPhone;
use yii\base\Model;

/**
 * Class UserMetaPhoneForm
 * @package sorokinmedia\user\forms
 *
 * @property int $country
 * @property int $number
 * @property bool $is_verified
 */
class UserMetaPhoneForm extends Model
{
    public $country;
    public $number;
    public $is_verified;

    /**
     * UserMetaPhoneForm constructor.
     * @param array $config
     * @param UserMetaPhone|null $userMetaPhone
     */
    public function __construct(array $config = [], UserMetaPhone $userMetaPhone = null)
    {
        if (!is_null($userMetaPhone)){
            $this->country = $userMetaPhone->country;
            $this->number = $userMetaPhone->number;
            $this->is_verified = $userMetaPhone->is_verified;
        }
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
            [['number'], 'unique'],
            [['is_verified'], 'boolean'],
            [['is_verified'], 'default', 'value' => false]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'country' => \Yii::t('app', 'Код страны'),
            'number' => \Yii::t('app', 'Номер телефона'),
            'is_verified' => \Yii::t('app', 'Подтвержден'),
        ];
    }
}