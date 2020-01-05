<?php

namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\UserMeta\json\UserMetaFullName;
use Yii;
use yii\base\Model;

/**
 * Class UserMetaFullNameForm
 * @package sorokinmedia\user\forms
 *
 * @property string $surname
 * @property string $name
 * @property string $patronymic
 */
class UserMetaFullNameForm extends Model
{
    public $surname;
    public $name;
    public $patronymic;

    /**
     * UserMetaFullNameForm constructor.
     * @param array $config
     * @param UserMetaFullName|null $userMetaFullName
     */
    public function __construct(array $config = [], UserMetaFullName $userMetaFullName = null)
    {
        if ($userMetaFullName !== null) {
            $this->surname = $userMetaFullName->surname;
            $this->name = $userMetaFullName->name;
            $this->patronymic = $userMetaFullName->patronymic;
        }
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['surname', 'name'], 'required'],
            [['surname', 'name', 'patronymic'], 'string', 'max' => 255]
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
            'patronymic' => Yii::t('sm-user', 'Отчетсво'),
        ];
    }
}
