<?php

namespace sorokinmedia\user\tests\entities\User;

use sorokinmedia\user\entities\User\AbstractUser;
use Yii;

class User extends AbstractUser
{
    use RelationClassTrait;

    public const ROLE_ADMIN = 'roleAdmin';
    public const ROLE_OWNER = 'roleOwner';
    public const ROLE_WORKER = 'roleWorker';

    public static function getRoleLink(string $role = null)
    {
        $roles = [
            self::ROLE_ADMIN => 'admin'
        ];
        if ($role !== null) {
            return $roles[$role];
        }
        return $roles;
    }

    public static function getRolesArray(string $role = null)
    {
        $roles = [
            self::ROLE_ADMIN => Yii::t('app', 'Администратор')
        ];
        if ($role !== null) {
            return $roles[$role];
        }
        return $roles;
    }

    public function afterSignUp(string $role = null)
    {
        return true;
    }

    public function afterSignUpEmail(string $role = null)
    {
        return true;
    }

    public function sendEmailConfirmation(): bool
    {
        return true;
    }

    public function sendEmailConfirmationWithPassword(string $password): bool
    {
        return true;
    }

    public function sendPasswordResetMail(): bool
    {
        return true;
    }

    public function getPrimaryRole(): string
    {
        return self::ROLE_ADMIN;
    }

    public function telegramOn(): bool
    {
        return true;
    }

    public function telegramOff(): bool
    {
        return true;
    }
}
