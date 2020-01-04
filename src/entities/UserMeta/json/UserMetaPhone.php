<?php

namespace sorokinmedia\user\entities\UserMeta\json;

use sorokinmedia\user\forms\UserMetaPhoneForm;
use Yii;
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
        if ($form !== null) {
            $this->country = $form->country;
            $this->number = $form->number;
            $this->is_verified = $form->is_verified;
        }
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['country', 'number'], 'required'],
            [['country'], 'in', 'range' => array_keys(self::getCodes())],
            [['number'], 'match', 'pattern' => '/^(\d{9}|\d{10})$/'], // 9 или 10 цифр (9 - Украина, Беларусь, 10 - РФ, Казахстан)
            [['is_verified'], 'boolean'],
            [['is_verified'], 'default', 'value' => false]
        ];
    }

    /**
     * @return array
     */
    public static function getCodes(): array
    {
        return [
            7 => Yii::t('app', '+7'), // Россия Казахстан
            375 => Yii::t('app', '+375'), // Украина
            380 => Yii::t('app', '+380'), // Беларусь
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'country' => Yii::t('app', 'Код страны'),
            'number' => Yii::t('app', 'Номер телефона'),
            'is_verified' => Yii::t('app', 'Подтвержден'),
        ];
    }

    /**
     * верификация телефона
     */
    public function verifyPhone(): void
    {
        $this->is_verified = true;
    }
}
