<?php
namespace sorokinmedia\user\entities\UserMeta\json;

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