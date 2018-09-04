<?php
namespace sorokinmedia\user\handlers\User\actions;

/**
 * Class Unblock
 * @package sorokinmedia\user\handlers\User\actions
 */
class Unblock extends AbstractAction
{
    /**
     * @return bool
     */
    public function execute() : bool
    {
        $this->user->unblockUser();
        return true;
    }
}