<?php
namespace sorokinmedia\user\handlers\User;

use sorokinmedia\user\forms\SignupForm;
use sorokinmedia\user\forms\SignUpFormEmail;
use sorokinmedia\user\handlers\User\interfaces\{AddRole,
    Block,
    Create,
    CreateFromEmail,
    RevokeRole,
    Unblock,
    VerifyAccount};
use sorokinmedia\user\entities\User\UserInterface;
use yii\rbac\Role;

/**
 * Class UserHandler
 * @package sorokinmedia\user\handlers\User
 *
 * @property UserInterface $user
 */
class UserHandler implements Create, CreateFromEmail, VerifyAccount, Block, Unblock, AddRole, RevokeRole
{
    public $user;

    /**
     * UserHandler constructor.
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * регистрация пользователя через форму
     * @param SignupForm $signup_form
     * @return bool
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function create(SignupForm $signup_form) : bool
    {
        return (new actions\Create($this->user, $signup_form))->execute();
    }

    /**
     * регистрация пользователя через форму с email
     * @param SignUpFormEmail $sign_up_form_email
     * @return bool
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function createFromEmail(SignUpFormEmail $sign_up_form_email) : bool
    {
        return (new actions\CreateFromEmail($this->user, null, $sign_up_form_email))->execute();
    }

    /**
     * верификация аккаунта
     * @return bool
     */
    public function verifyAccount() : bool
    {
        return (new actions\VerifyAccount($this->user))->execute();
    }

    /**
     * блокировка пользователя
     * @return bool
     */
    public function block() : bool
    {
        return (new actions\Block($this->user))->execute();
    }

    /**
     * разблокировка пользователя
     * @return bool
     */
    public function unblock() : bool
    {
        return (new actions\Unblock($this->user))->execute();
    }

    /**
     * добавление роли
     * @param Role $role
     * @return bool
     */
    public function addRole(Role $role) : bool
    {
        return (new actions\AddRole($this->user, $role))->execute();
    }

    /**
     * удаление роли
     * @param Role $role
     * @return bool
     */
    public function revokeRole(Role $role) : bool
    {
        return (new actions\RevokeRole($this->user, $role))->execute();
    }
}