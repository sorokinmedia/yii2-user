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
}