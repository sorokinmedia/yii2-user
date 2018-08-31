<?php
namespace sorokinmedia\user\handlers\User;

use sorokinmedia\user\forms\SignupForm;
use sorokinmedia\user\handlers\User\interfaces\{Create, VerifyAccount};
use sorokinmedia\user\entities\User\UserInterface;

/**
 * Class UserHandler
 * @package sorokinmedia\user\handlers\User
 *
 * @property UserInterface $user
 */
class UserHandler implements Create, VerifyAccount
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
     * верификация аккаунта
     * @return bool
     */
    public function verifyAccount() : bool
    {
        return (new actions\VerifyAccount($this->user))->execute();
    }
}