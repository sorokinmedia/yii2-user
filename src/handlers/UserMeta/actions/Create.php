<?php

namespace sorokinmedia\user\handlers\UserMeta\actions;

use sorokinmedia\user\entities\UserMeta\AbstractUserMeta;
use Throwable;
use yii\db\Exception;

/**
 * Class Create
 * @package sorokinmedia\user\handlers\UserMeta\actions
 *
 * @property AbstractUserMeta $user_meta
 */
class Create extends AbstractAction
{
    /**
     * @return bool
     * @throws Throwable
     * @throws Exception
     */
    public function execute(): bool
    {
        $this->user_meta->insertModel();
        return true;
    }
}
