<?php
namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\UserMeta\json\UserMetaFullName;
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
        if (!is_null($userMetaFullName)){
            $this->surname = $userMetaFullName->surname;
            $this->name = $userMetaFullName->name;
            $this->patronymic = $userMetaFullName->patronymic;
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['surname', 'name'], 'required'],
            [['surname', 'name', 'patronymic'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'surname' => \Yii::t('app', 'Фамилия'),
            'name' => \Yii::t('app', 'Имя'),
            'patronymic' => \Yii::t('app', 'Отчетсво'),
        ];
    }
}