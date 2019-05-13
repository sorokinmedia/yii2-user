<?php
namespace sorokinmedia\user\handlers\CompanyUser\actions;

/**
 * Class AddPermission
 * @package sorokinmedia\user\handlers\CompanyUser\actions
 */
class AddPermission extends AbstractActionWithPermission
{
    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function execute() : bool
    {
        $this->company_user->addPermission($this->permission);
        $this->company_user->afterAddPermission($this->permission);
        return true;
    }
}
