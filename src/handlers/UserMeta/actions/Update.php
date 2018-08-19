<?php
namespace sorokinmedia\user\handlers\UserMeta\actions;

use sorokinmedia\user\entities\UserMeta\AbstractUserMeta;

/**
 * Class Update
 * @package sorokinmedia\user\handlers\UserMeta\actions
 *
 * @property AbstractUserMeta $user_meta
 */
class Update extends AbstractAction
{
    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function execute() : bool
    {
        $this->user_meta->updateModel();
        return true;
    }
}