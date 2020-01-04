<?php

namespace sorokinmedia\user\handlers\CompanyUser\actions;

use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;

/**
 * Class Delete
 * @package sorokinmedia\user\handlers\CompanyUser\actions
 */
class Delete extends AbstractAction
{
    /**
     * @return bool
     * @throws Throwable
     * @throws Exception
     * @throws StaleObjectException
     */
    public function execute(): bool
    {
        $this->company_user->deleteModel();
        return true;
    }
}
