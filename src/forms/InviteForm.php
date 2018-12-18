<?php

namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\Company\AbstractCompany;
use sorokinmedia\user\entities\CompanyUser\AbstractCompanyUser;
use sorokinmedia\user\entities\User\AbstractUser;
use sorokinmedia\user\entities\UserInvite\AbstractUserInvite;
use sorokinmedia\user\handlers\UserInvite\UserInviteHandler;
use yii\base\Model;
use yii\rbac\Role;

class InviteForm extends Model
{
    /** @var AbstractCompany */
    public $company;
    /** @var AbstractUser */
    public $initiator;
    /** @var AbstractUser */
    public $user;
    /** @var string */
    public $email;
    /** @var Role */
    public $role;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['email', 'role', 'company', 'initiator'], 'required'],
            [['email'], 'email'],
            [['company'], 'exist', 'targetClass' => AbstractCompany::class, 'targetAttribute' => 'id'],
            [['user'], 'exist', 'targetClass' => AbstractUser::class, 'targetAttribute' => 'id'],
            [['email', 'user'], 'checkAtLeast'],
            [['email'], 'checkExistingInvite'],
            [['email'], 'checkExistingLink']
        ];
    }

    public function checkAtLeast($attribute): void
    {
        if (!$this->user && !$this->email) {
            $this->addError($attribute, \Yii::t('app', 'Пользователь или email должны быть заполнены'));
        }
    }

    public function checkExistingInvite($attribute)
    {
        $query = AbstractUserInvite::find()->where([
            'company_id' => $this->company->id,
            'status' => AbstractUserInvite::STATUS_NEW,
            'role' => $this->role
        ])->andWhere(['user_email' => $this->email]);

        if ($this->user) {
            $query->orWhere(['user_id' => $this->user->id]);
        }

        if ($query->exists()) {
            $this->addError($attribute, \Yii::t('app', 'Пришглашение этому пользователю уже отправлено'));
        }
    }

    public function checkExistingLink($attribute)
    {
        if ($this->user){
            $query = AbstractCompanyUser::find()->where([
                'company_id' => $this->company->id,
                'user_id' => $this->user->id,
                'role' => $this->role,
            ]);

            if ($query->exists()) {
                $this->addError($attribute, \Yii::t('app', 'Пользователю уже выданы права'));
            }
        }
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function invite(): bool
    {
        if ($this->user) {
            return (new UserInviteHandler())->inviteExistingUser($this);
        }

        return (new UserInviteHandler())->inviteNewUser($this);
    }

}