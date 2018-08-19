<?php
namespace sorokinmedia\user\handlers\User;

use common\components\user\forms\RegisterForm;
use sorokinmedia\user\handlers\User\interfaces\{Create};
use sorokinmedia\user\entities\User\UserInterface;


/**
 * Class UserHandler
 * @package sorokinmedia\user\handlers\User
 *
 * @property UserInterface $user
 */
class UserHandler implements Create
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
     * @param RegisterForm $signup_form
     * @return bool
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function create(RegisterForm $signup_form) : bool
    {
        return (new actions\Create($this->user, $signup_form))->execute();
    }
}