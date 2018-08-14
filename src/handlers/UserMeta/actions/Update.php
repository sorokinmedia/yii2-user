<?php
namespace sorokinmedia\user\handlers\UserMeta\actions;

/**
 * Class Update
 * @package sorokinmedia\user\handlers\UserMeta\actions
 */
class Update extends AbstractAction
{
    /**
     * @return bool
     */
    public function execute() : bool
    {
        $this->user_meta->updateModel();
        return true;
    }
}