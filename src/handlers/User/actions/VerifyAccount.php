<?php
namespace sorokinmedia\user\handlers\User\actions;

/**
 * Class VerifyAccount
 * @package sorokinmedia\user\handlers\User\actions
 */
class VerifyAccount extends AbstractAction
{
    /**
     * @return bool
     */
    public function execute() : bool
    {
        $this->user->verifyAccount();
        return true;
    }
}