<?php

namespace sorokinmedia\user\handlers\CompanyUser\actions;

use yii\db\Exception;

/**
 * Class RemovePermission
 * @package sorokinmedia\user\handlers\CompanyUser\actions
 */
class RemovePermission extends AbstractActionWithPermission
{
    /**
     * @return bool
     * @throws Exception
     */
    public function execute(): bool
    {
        $this->company_user->removePermission($this->permission);
        $this->company_user->afterRemovePermission($this->permission);
        return true;
    }
}
