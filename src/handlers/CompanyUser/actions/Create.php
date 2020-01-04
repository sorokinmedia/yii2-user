<?php

namespace sorokinmedia\user\handlers\CompanyUser\actions;

use Throwable;
use yii\db\Exception;

/**
 * Class Create
 * @package sorokinmedia\user\handlers\CompanyUser\actions
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
        $this->company_user->insertModel();
        return true;
    }
}
