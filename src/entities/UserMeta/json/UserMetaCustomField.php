<?php
namespace sorokinmedia\user\entities\UserMeta\json;

use yii\base\Model;

/**
 * Class UserMetaCustomField
 * @package sorokinmedia\user\entities\UserMeta\json
 *
 * @property string $name
 * @property string $value
 */
class UserMetaCustomField extends Model
{
    public $name;
    public $value;

    /**
     * UserMetaCustomField constructor.
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
            [['name'], 'string', 'max' => 255],
            [['value'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => \Yii::t('app', 'Название поля'),
            'value' => \Yii::t('app', 'Значение поля'),
        ];
    }
}