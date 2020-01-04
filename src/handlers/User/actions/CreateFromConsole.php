<?php

namespace sorokinmedia\user\handlers\User\actions;

use sorokinmedia\user\entities\User\AbstractUser;
use yii\web\ServerErrorHttpException;

/**
 * Class CreateFromConsole
 * @package sorokinmedia\user\handlers\User\actions
 *
 * @property AbstractUser $user
 */
class CreateFromConsole extends AbstractAction
{
    /**
     * @return bool
     * @throws ServerErrorHttpException
     */
    public function execute(): bool
    {
        $this->user->signUpConsole($this->signup_form);
        return true;
    }
}
