<?php

namespace sorokinmedia\user\handlers\User;

use sorokinmedia\user\entities\User\UserInterface;
use sorokinmedia\user\forms\{SignupForm, SignUpFormConsole, SignUpFormEmail, SignUpFormExisted};
use sorokinmedia\user\handlers\User\interfaces\{AddRole,
    Block,
    Create,
    CreateFromConsole,
    CreateFromEmail,
    RevokeRole,
    Unblock,
    VerifyAccount
};
use yii\rbac\Role;
use yii\web\ServerErrorHttpException;

/**
 * Class UserHandler
 * @package sorokinmedia\user\handlers\User
 *
 * @property UserInterface $user
 */
class UserHandler implements Create, CreateFromEmail, CreateFromConsole, VerifyAccount, Block, Unblock, AddRole, RevokeRole
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
     * @throws ServerErrorHttpException
     */
    public function create(SignupForm $signup_form): bool
    {
        return (new actions\Create($this->user, $signup_form))->execute();
    }

    /**
     * регистрация пользователя через форму с email
     * @param SignUpFormEmail $sign_up_form_email
     * @return bool
     * @throws ServerErrorHttpException
     */
    public function createFromEmail(SignUpFormEmail $sign_up_form_email): bool
    {
        return (new actions\CreateFromEmail($this->user, null, $sign_up_form_email))->execute();
    }

    /**
     * регистрация пользователя через форму консольной регистрации
     * @param SignUpFormConsole $sign_up_form_console
     * @return bool
     * @throws ServerErrorHttpException
     */
    public function createFromConsole(SignUpFormConsole $sign_up_form_console): bool
    {
        return (new actions\CreateFromConsole($this->user, null, null, $sign_up_form_console))->execute();
    }

    /**
     * регистрация пользователя через перенос пользователя с проекта на проект
     * @param SignUpFormExisted $sign_up_form_existed
     * @return bool
     * @throws ServerErrorHttpException
     */
    public function createExisted(SignUpFormExisted $sign_up_form_existed): bool
    {
        return (new actions\CreateExisted($this->user, null, null, null, $sign_up_form_existed))->execute();
    }

    /**
     * верификация аккаунта
     * @return bool
     */
    public function verifyAccount(): bool
    {
        return (new actions\VerifyAccount($this->user))->execute();
    }

    /**
     * блокировка пользователя
     * @return bool
     */
    public function block(): bool
    {
        return (new actions\Block($this->user))->execute();
    }

    /**
     * разблокировка пользователя
     * @return bool
     */
    public function unblock(): bool
    {
        return (new actions\Unblock($this->user))->execute();
    }

    /**
     * добавление роли
     * @param Role $role
     * @return bool
     */
    public function addRole(Role $role): bool
    {
        return (new actions\AddRole($this->user, $role))->execute();
    }

    /**
     * удаление роли
     * @param Role $role
     * @return bool
     */
    public function revokeRole(Role $role): bool
    {
        return (new actions\RevokeRole($this->user, $role))->execute();
    }
}
