<?php
namespace sorokinmedia\user\handlers\CompanyUser\actions;

/**
 * Class Create
 * @package sorokinmedia\user\handlers\CompanyUser\actions
 */
class Create extends AbstractAction
{
    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function execute() : bool
    {
        $this->company_user->insertModel();
        return true;
    }
}