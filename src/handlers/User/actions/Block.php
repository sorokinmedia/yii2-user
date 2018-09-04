<?php
namespace sorokinmedia\user\handlers\User\actions;

/**
 * Class Block
 * @package sorokinmedia\user\handlers\User\actions
 */
class Block extends AbstractAction
{
    /**
     * @return bool
     */
    public function execute() : bool
    {
        $this->user->blockUser();
        return true;
    }
}