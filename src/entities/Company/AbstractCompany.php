<?php

namespace sorokinmedia\user\entities\Company;

use sorokinmedia\ar_relations\RelationInterface;
use sorokinmedia\user\entities\{CompanyUser\AbstractCompanyUser, User\AbstractUser, User\UserInterface};
use sorokinmedia\user\forms\CompanyUserForm;
use sorokinmedia\user\handlers\CompanyUser\CompanyUserHandler;
use Throwable;
use Yii;
use yii\db\{ActiveQuery, ActiveRecord, Exception, StaleObjectException};

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
     * @return string
     */
    public static function tableName(): string
    {
        return 'company';
    }

    /**
     * статический конструктор
     * @param UserInterface $owner
     * @param string $role
     * @return CompanyInterface
     * @throws Exception
     * @throws Throwable
     */
    public static function create(UserInterface $owner, string $role): CompanyInterface
    {
        /** @var AbstractUser $owner */
        $company = static::find()->where(['owner_id' => $owner->id])->one();
        if ($company instanceof AbstractCompany) {
            return $company;
        }
        $company = new static([
            'owner_id' => $owner->id,
        ]);
        if (!$company->insert()) {
            throw new Exception(Yii::t('app', 'Ошибка при добавлении компании'));
        }
        $company->refresh();
        $form = new CompanyUserForm([
            'company_id' => $company->id,
            'user_id' => $company->owner_id,
            'role' => $role,
        ]);
        $company_user = new $company->__companyUserClass([], $form);
        if (!(new CompanyUserHandler($company_user))->create()) {
            throw new Exception(Yii::t('app', 'Ошибка при добавлении сотрудника в компанию'));
        }
        return $company;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['owner_id'], 'required'],
            [['owner_id'], 'exist', 'targetClass' => AbstractUser::class, 'targetAttribute' => ['owner_id' => 'id']],
            [['name'], 'default', 'value' => Yii::t('app', 'Моя компания')],
            [['name'], 'string', 'max' => 500],
            [['description'], 'string']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'owner_id' => Yii::t('app', 'Владелец'),
            'name' => Yii::t('app', 'Название'),
            'description' => Yii::t('app', 'Описание'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getOwner(): ActiveQuery
    {
        return $this->hasOne($this->__userClass, ['id' => 'owner_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUsers(): ActiveQuery
    {
        return $this->hasMany($this->__companyUserClass, ['company_id' => 'id']);
    }

    /**
     * @return array
     */
    public function getUserIdsArray(): array
    {
        return $this->__companyUserClass::find()
            ->select(['user_id'])
            ->where(['company_id' => $this->id])
            ->column();
    }

    /**
     * @return bool
     * @throws Exception
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function deleteModel(): bool
    {
        foreach ($this->users as $companyUser) {
            if ($companyUser instanceof AbstractCompanyUser) {
                (new CompanyUserHandler($companyUser))->delete();
            }
        }
        $this->delete();
        return true;
    }
}
