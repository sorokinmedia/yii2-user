<?php
namespace sorokinmedia\user\entities\Company;

use sorokinmedia\user\entities\{
    CompanyUser\AbstractCompanyUser,User\AbstractUser, User\UserInterface
};
use sorokinmedia\user\forms\CompanyUserForm;
use sorokinmedia\user\handlers\CompanyUser\CompanyUserHandler;
use sorokinmedia\ar_relations\RelationInterface;
use yii\db\{
    ActiveQuery,ActiveRecord,Exception
};

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property int $owner_id
 * @property string $name
 * @property string $description
 *
 * @property AbstractUser $owner
 * @property AbstractCompanyUser[] $users
 * @property array $userIdsArray
 */
abstract class AbstractCompany extends ActiveRecord implements CompanyInterface, RelationInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['owner_id'], 'required'],
            [['owner_id'], 'exist', 'targetClass' => AbstractUser::class, 'targetAttribute' => ['owner_id' => 'id']],
            [['name'], 'default', 'value' => \Yii::t('app', 'Моя компания')],
            [['name'], 'string', 'max' => 500],
            [['description'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'owner_id' => \Yii::t('app', 'Владелец'),
            'name' => \Yii::t('app', 'Название'),
            'description' => \Yii::t('app', 'Описание'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getOwner() : ActiveQuery
    {
        return $this->hasOne($this->__userClass, ['id' => 'owner_id']);
    }

    /**
     * @return array
     */
    public function getUsers() : array
    {
        return $this->hasMany($this->__companyUserClass, ['company_id' => 'id']);
    }

    /**
     * @return array
     */
    public function getUserIdsArray() : array
    {
        return $this->__companyUserClass::find()
            ->select(['user_id'])
            ->where(['company_id' => $this->id])
            ->column();
    }

    /**
     * статический конструктор
     * @param UserInterface $owner
     * @param string $role
     * @return CompanyInterface
     * @throws Exception
     * @throws \Throwable
     */
    public static function create(UserInterface $owner, string $role) : CompanyInterface
    {
        /** @var AbstractUser $owner */
        $company = static::find()->where(['owner_id' => $owner->id])->one();
        if ($company instanceof AbstractCompany){
            return $company;
        }
        $company = new static([
            'owner_id' => $owner->id,
        ]);
        if (!$company->insert()){
            throw new Exception(\Yii::t('app', 'Ошибка при добавлении компании'));
        }
        $company->refresh();
        $form = new CompanyUserForm([
            'company_id' => $company->id,
            'user_id' => $company->owner_id,
            'role' => $role,
        ]);
        $company_user = new $company->__companyUserClass([], $form);
        if (!(new CompanyUserHandler($company_user))->create()){
            throw new Exception(\Yii::t('app', 'Ошибка при добавлении сотрудника в компанию'));
        }
        return $company;
    }
}
