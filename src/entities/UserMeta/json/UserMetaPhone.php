<?php
namespace sorokinmedia\user\entities\UserMeta\json;

use sorokinmedia\user\forms\UserMetaPhoneForm;
use yii\base\Model;

/**
 * Class UserMetaPhone
 * @package sorokinmedia\user\entities\UserMeta\json
 *
 * @property int $country
 * @property int $number
 * @property bool $is_verified
 */
class UserMetaPhone extends Model
{
    public $country;
    public $number;
    public $is_verified;

    /**
     * UserMetaPhone constructor.
     * @param array $config
     * @param UserMetaPhoneForm|null $form
     */
    public function __construct(array $config = [], UserMetaPhoneForm $form = null)
    {
        parent::__construct($config);
        if (!is_null($form)){
            $this->country = $form->country;
            $this->number = $form->number;
            $this->is_verified = $form->is_verified;
        }
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

    /**
     * верификация телефона
     */
    public function verifyPhone()
    {
        $this->is_verified = true;
    }
}