<?php
namespace sorokinmedia\user\handlers\User\actions;

use sorokinmedia\user\entities\User\AbstractUser;

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
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function execute() : bool
    {
        $this->user->signUp($this->signup_form);
        return true;
    }
}