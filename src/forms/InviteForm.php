<?php

namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\Company\AbstractCompany;
use sorokinmedia\user\entities\CompanyUser\AbstractCompanyUser;
use sorokinmedia\user\entities\User\AbstractUser;
use sorokinmedia\user\entities\UserInvite\AbstractUserInvite;
use sorokinmedia\user\handlers\UserInvite\UserInviteHandler;
use yii\base\Model;
use yii\db\Expression;
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

    /** @var array */
    public $meta;

    /** @var UserInviteHandler */
    public $inviteHandler;

    public function __construct(UserInviteHandler $handler, array $config = [])
    {
        $this->inviteHandler = $handler;

        parent::__construct($config);
    }

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
            [['email'], 'checkExistingLink'],
            [['meta'], 'safe']
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
            'role' => $this->role,
            'meta' => new Expression('CAST(:meta as JSON)', [':meta' => json_encode($this->meta)])
        ]);

        if ($this->user) {
            $query->andWhere(['or', ['user_email' => $this->email], ['user_id' => $this->user->id]]);
        }else{
            $query->andWhere(['user_email' => $this->email]);
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

            if (empty($this->meta) && $query->exists()) {
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
            return $this->inviteHandler->inviteExistingUser($this);
        }

        return $this->inviteHandler->inviteNewUser($this);
    }

}