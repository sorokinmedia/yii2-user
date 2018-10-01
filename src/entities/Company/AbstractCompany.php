<?php
namespace sorokinmedia\user\entities\Company;

use common\components\company\entities\CompanyUser\CompanyUser;
use sorokinmedia\user\entities\User\AbstractUser;
use sorokinmedia\ar_relations\RelationInterface;
use sorokinmedia\user\entities\User\UserInterface;
use sorokinmedia\user\forms\CompanyUserForm;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * This is the model class for table "company".
 *
 * @property int $id
 * @property int $owner_id
 * @property string $name
 * @property string $description
 *
 * @property AbstractUser $owner
 * @property CompanyUser[] $users
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
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => AbstractUser::class, 'targetAttribute' => ['owner_id' => 'id']],
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
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne($this->__userClass, ['id' => 'owner_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany($this->__companyUserClass, ['company_id' => 'id']);
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
        $company_user = new CompanyUser([], $form);
        if (!(new CompanyUserHandler($company_user))->create()){
            throw new Exception(\Yii::t('app', 'Ошибка при добавлении сотрудника в компанию'));
        }
        return $company;
    }
}