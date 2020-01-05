<?php

namespace sorokinmedia\user\handlers\User\actions;

use sorokinmedia\user\entities\User\AbstractUser;
use yii\web\ServerErrorHttpException;

/**
 * Class CreateExisted
 * @package sorokinmedia\user\handlers\User\actions
 *
 * @property AbstractUser $user
 *
 * метод для переноса пользователей между проектами
 * на вход получает username и password_hash
 * отличается тем, что не генерится новый хеш пароля
 * все остальное как в обычной регистрации
 */
class CreateExisted extends AbstractAction
{
    /**
     * @return bool
     * @throws ServerErrorHttpException
     */
    public function execute(): bool
    {
        $this->user->signUpExisted($this->signup_form);
        return true;
    }
}
