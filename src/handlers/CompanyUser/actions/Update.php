<?php

namespace sorokinmedia\user\handlers\CompanyUser\actions;

use yii\db\Exception;

/**
 * Class Update
 * @package sorokinmedia\user\handlers\CompanyUser\actions
 */
class Update extends AbstractAction
{
    /**
     * @return bool
     * @throws Exception
     */
    public function execute(): bool
    {
        $this->company_user->updateModel();
        return true;
    }
}
