<?php

namespace sorokinmedia\user\handlers\User\actions;

use sorokinmedia\user\entities\User\AbstractUser;
use yii\web\ServerErrorHttpException;

/**
 * Class CreateFromEmail
 * @package sorokinmedia\user\handlers\User\actions
 *
 * @property AbstractUser $user
 */
class CreateFromEmail extends AbstractAction
{
    /**
     * @return bool
     * @throws ServerErrorHttpException
     */
    public function execute(): bool
    {
        $this->user->signUpEmail($this->signup_form);
        return true;
    }
}
