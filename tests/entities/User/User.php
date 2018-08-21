<?php
namespace sorokinmedia\user\tests\entities\User;

use sorokinmedia\user\entities\User\AbstractUser;

class User extends AbstractUser
{
    use RelationClassTrait;

    public function afterSignUp()
    {
        return true;
    }

    public function sendEmailConfirmation() : bool
    {
        return true;
    }

    public function sendPasswordResetMail() : bool
    {
        return true;
    }

    public function getPrimaryRole() : string
    {
        return 'roleAdmin';
    }

    public static function getRoleLink(string $role = null)
    {
        $roles = [
            'roleAdmin' => 'admin'
        ];
        if (!is_null($role)){
            return $roles[$role];
        }
        return $roles;
    }

    public static function getRolesArray(string $role = null)
    {
        $roles = [
            'roleAdmin' => \Yii::t('app', 'Администратор')
        ];
        if (!is_null($role)){
            return $roles[$role];
        }
        return $roles;
    }
}