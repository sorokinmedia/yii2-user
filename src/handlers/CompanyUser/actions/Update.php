<?php
namespace sorokinmedia\user\handlers\CompanyUser\actions;

/**
 * Class Update
 * @package sorokinmedia\user\handlers\CompanyUser\actions
 */
class Update extends AbstractAction
{
    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function execute() : bool
    {
        $this->company_user->updateModel();
        return true;
    }
}