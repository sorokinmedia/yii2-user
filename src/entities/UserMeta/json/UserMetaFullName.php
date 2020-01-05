<?php

namespace sorokinmedia\user\entities\UserMeta\json;

use Yii;
use yii\base\Model;

/**
 * Class UserMetaFullName
 * @package sorokinmedia\user\entities\UserMeta\json
 *
 * @property string $surname
 * @property string $name
 * @property string $patronymic
 */
class UserMetaFullName extends Model
{
    public $surname;
    public $name;
    public $patronymic;

    /**
     * UserMetaFullName constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['surname', 'name', 'patronymic'], 'string'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'surname' => Yii::t('sm-user', 'Фамилия'),
            'name' => Yii::t('sm-user', 'Имя'),
            'patronymic' => Yii::t('sm-user', 'Отчество'),
        ];
    }
}
