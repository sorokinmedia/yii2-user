<?php
namespace sorokinmedia\user\tests\entities\User;

use sorokinmedia\user\entities\User\AbstractUser;

class User extends AbstractUser
{
    use RelationClassTrait;

    const ROLE_ADMIN = 'roleAdmin';

    public function afterSignUp()
    {
        return true;
    }

    public function afterSignUpEmail()
    {
        return true;
    }

    public function sendEmailConfirmation() : bool
    {
        return true;
    }

    public function sendEmailConfirmationWithPassword() : bool
    {
        return true;
    }

    public function sendPasswordResetMail() : bool
    {
        return true;
    }

    public function getPrimaryRole() : string
    {
        return self::ROLE_ADMIN;
    }

    public static function getRoleLink(string $role = null)
    {
        $roles = [
            self::ROLE_ADMIN => 'admin'
        ];
        if (!is_null($role)){
            return $roles[$role];
        }
        return $roles;
    }

    public static function getRolesArray(string $role = null)
    {
        $roles = [
            self::ROLE_ADMIN => \Yii::t('app', 'Администратор')
        ];
        if (!is_null($role)){
            return $roles[$role];
        }
        return $roles;
    }
}