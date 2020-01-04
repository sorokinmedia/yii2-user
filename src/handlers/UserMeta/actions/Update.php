<?php

namespace sorokinmedia\user\handlers\UserMeta\actions;

use sorokinmedia\user\entities\UserMeta\AbstractUserMeta;
use yii\db\Exception;

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
     * @throws Exception
     */
    public function execute(): bool
    {
        $this->user_meta->updateModel();
        return true;
    }
}
