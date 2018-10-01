<?php
namespace sorokinmedia\user\handlers\CompanyUser\actions;

/**
 * Class Delete
 * @package sorokinmedia\user\handlers\CompanyUser\actions
 */
class Delete extends AbstractAction
{
    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function execute() : bool
    {
        $this->company_user->deleteModel();
        return true;
    }
}