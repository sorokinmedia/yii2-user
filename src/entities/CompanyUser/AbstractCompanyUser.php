<?php

namespace sorokinmedia\user\entities\CompanyUser;

use sorokinmedia\ar_relations\RelationInterface;
use sorokinmedia\user\entities\{Company\AbstractCompany, User\AbstractUser};
use sorokinmedia\user\forms\CompanyUserForm;
use yii\db\{ActiveQuery, ActiveRecord, Exception, StaleObjectException};
use Throwable;
use Yii;
use yii\rbac\Role;

/**
 * This is the model class for table "company_user".
 *
 * @property int $company_id
 * @property int $user_id
 * @property string $role
 * @property array $permissions
 *
 * @property AbstractCompany $company
 * @property AbstractUser $user
 * @property Role $roleObject
 *
 * @property CompanyUserForm $form
 */
abstract class AbstractCompanyUser extends ActiveRecord implements CompanyUserInterface, RelationInterface
{
    public $form;

    /**
     * CompanyUser constructor.
     * @param array $config
     * @param CompanyUserForm|null $form
     */
    public function __construct(array $config = [], CompanyUserForm $form = null)
    {
        if ($form !== null) {
            $this->form = $form;
        }
        parent::__construct($config);
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'company_user';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['company_id', 'user_id', 'role'], 'required'],
            [['company_id', 'user_id'], 'integer'],
            [['role'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'targetClass' => AbstractUser::class, 'targetAttribute' => ['user_id' => 'id']],
            [['company_id'], 'exist', 'targetClass' => AbstractCompany::class, 'targetAttribute' => ['company_id' => 'id']],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'company_id' => Yii::t('sm-user', 'Компания'),
            'user_id' => Yii::t('sm-user', 'Пользователь'),
            'role' => Yii::t('sm-user', 'Роль'),
            'permissions' => Yii::t('sm-user', 'Разрешения'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne($this->__userClass, ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCompany(): ActiveQuery
    {
        return $this->hasOne($this->__companyClass, ['id' => 'company_id']);
    }

    /**
     * @return null|Role
     */
    public function getRoleObject(): ?Role
    {
        return Yii::$app->authManager->getRole($this->role);
    }

    /**
     * добавление модели в БД
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    public function insertModel(): bool
    {
        $this->getFromForm();
        if (!$this->insert()) {
            throw new Exception(Yii::t('sm-user', 'Ошибка при добавлении в БД'));
        }
        return true;
    }

    /**
     * трансфер данных из формы в модель
     */
    public function getFromForm(): void
    {
        if ($this->form !== null) {
            $this->company_id = $this->form->company_id;
            $this->user_id = $this->form->user_id;
            $this->role = $this->form->role;
        }
    }

    /**
     * удаление из БД
     * @return bool
     * @throws Exception
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function deleteModel(): bool
    {
        if (!$this->delete()) {
            throw new Exception(Yii::t('sm-user', 'Ошибка при удалении из БД'));
        }
        return true;
    }

    /**
     * добавление дополнительного разрешения
     * @param AbstractCompanyUserPermission $permission
     * @return bool
     * @throws Exception
     */
    public function addPermission(AbstractCompanyUserPermission $permission): bool
    {
        if (!empty($this->permissions)) {
            $this->permissions = array_merge($this->permissions, [$permission]);
        } else {
            $this->permissions = [$permission];
        }
        return $this->updateModel();
    }

    /**
     * обновление модели в бд
     * @return bool
     * @throws Exception
     */
    public function updateModel(): bool
    {
        $this->getFromForm();
        if (!$this->save()) {
            throw new Exception(Yii::t('sm-user', 'Ошибка при обновлении в БД'));
        }
        return true;
    }

    /**
     * вызывается после добавления пермишена
     * можно переопределить на проекте
     * @param AbstractCompanyUserPermission $permission
     * @return bool
     */
    public function afterAddPermission(AbstractCompanyUserPermission $permission): bool
    {
        return true;
    }

    /**
     * удаление дополнительного разрешения
     * @param AbstractCompanyUserPermission $permission
     * @return bool
     * @throws Exception
     */
    public function removePermission(AbstractCompanyUserPermission $permission): bool
    {
        if (empty($this->permissions)) {
            return true;
        }
        $permissions = $this->permissions;
        $key = array_search($permission->id, array_column($permissions, 'id'), true);
        unset($permissions[$key]); // удаление элемента
        $permissions = array_values($permissions);
        $this->permissions = $permissions;
        return $this->updateModel();
    }

    /**
     * вызывается после удаления пермишена
     * можно переодпределить на проекте
     * @param AbstractCompanyUserPermission $permission
     * @return bool
     */
    public function afterRemovePermission(AbstractCompanyUserPermission $permission): bool
    {
        return true;
    }
}
