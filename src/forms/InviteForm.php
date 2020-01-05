<?php

namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\{Company\AbstractCompany,
    CompanyUser\AbstractCompanyUser,
    User\AbstractUser,
    UserInvite\AbstractUserInvite
};
use sorokinmedia\user\handlers\UserInvite\UserInviteHandler;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\db\Expression;
use yii\rbac\Role;

/**
 * Class InviteForm
 * @package sorokinmedia\user\forms
 *
 * @property AbstractCompany $company
 * @property AbstractUser $initiator
 * @property AbstractUser $user
 * @property string $email
 * @property Role $role
 * @property array $meta
 * @property UserInviteHandler $inviteHandler
 */
class InviteForm extends Model
{
    public $company;
    public $initiator;
    public $user;
    public $email;
    public $role;
    public $meta;
    public $inviteHandler;

    /**
     * InviteForm constructor.
     * @param UserInviteHandler $handler
     * @param array $config
     */
    public function __construct(UserInviteHandler $handler, array $config = [])
    {
        $this->inviteHandler = $handler;

        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['email', 'role', 'company', 'initiator'], 'required'],
            [['email'], 'email'],
            [['company'], 'exist', 'targetClass' => AbstractCompany::class, 'targetAttribute' => 'id'],
            [['user'], 'exist', 'targetClass' => AbstractUser::class, 'targetAttribute' => 'id'],
            [['email', 'user'], 'checkAtLeast'],
            [['email'], 'checkExistingInvite'],
            [['email'], 'checkExistingLink'],
            [['meta'], 'safe']
        ];
    }

    /**
     * @param $attribute
     */
    public function checkAtLeast($attribute): void
    {
        if (!$this->user && !$this->email) {
            $this->addError($attribute, Yii::t('app-sm-user', 'Пользователь или email должны быть заполнены'));
        }
    }

    /**
     * @param $attribute
     */
    public function checkExistingInvite($attribute)
    {
        $query = AbstractUserInvite::find()->where([
            'company_id' => $this->company->id,
            'status' => AbstractUserInvite::STATUS_NEW,
            'role' => $this->role,
            'meta' => new Expression('CAST(:meta as JSON)', [':meta' => json_encode($this->meta)])
        ]);

        if ($this->user) {
            $query->andWhere(['or', ['user_email' => $this->email], ['user_id' => $this->user->id]]);
        } else {
            $query->andWhere(['user_email' => $this->email]);
        }

        if ($query->exists()) {
            $this->addError($attribute, Yii::t('app-sm-user', 'Пришглашение этому пользователю уже отправлено'));
        }
    }

    /**
     * @param $attribute
     */
    public function checkExistingLink($attribute)
    {
        if ($this->user) {
            $query = AbstractCompanyUser::find()->where([
                'company_id' => $this->company->id,
                'user_id' => $this->user->id,
                'role' => $this->role,
            ]);

            if (empty($this->meta) && $query->exists()) {
                $this->addError($attribute, Yii::t('app-sm-user', 'Пользователю уже выданы права'));
            }
        }
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     * @throws Exception
     */
    public function invite(): bool
    {
        if ($this->user) {
            return $this->inviteHandler->inviteExistingUser($this);
        }

        return $this->inviteHandler->inviteNewUser($this);
    }
}
