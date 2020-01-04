<?php

namespace sorokinmedia\user\handlers\User\actions;

use sorokinmedia\user\entities\User\AbstractUser;
use yii\web\ServerErrorHttpException;

/**
 * Class Create
 * @package sorokinmedia\user\handlers\User\actions
 *
 * @property AbstractUser $user
 */
class Create extends AbstractAction
{
    /**
     * @return bool
     * @throws ServerErrorHttpException
     */
    public function execute(): bool
    {
        $this->user->signUp($this->signup_form);
        return true;
    }
}
