<?php
namespace sorokinmedia\user\handlers\User\actions;

use sorokinmedia\user\entities\User\AbstractUser;

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
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function execute() : bool
    {
        $this->user->signUpEmail($this->signup_form);
        return true;
    }
}