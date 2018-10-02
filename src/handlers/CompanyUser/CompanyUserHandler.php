<?php
namespace sorokinmedia\user\handlers\CompanyUser;

use sorokinmedia\user\handlers\CompanyUser\interfaces\{Create, Delete, Update};
use sorokinmedia\user\entities\CompanyUser\AbstractCompanyUser;

/**
 * Class CompanyUserHandler
 * @package sorokinmedia\user\handlers\CompanyUser
 *
 * @property AbstractCompanyUser $company_user
 */
class CompanyUserHandler implements Create, Update, Delete
{
    public $company_user;

    /**
     * CompanyUserHandler constructor.
     * @param AbstractCompanyUser $company_user
     */
    public function __construct(AbstractCompanyUser $company_user)
    {
        $this->company_user = $company_user;
        return $this;
    }

    /**
     * @return bool
     * @throws \Throwable
     */
    public function create() : bool
    {
        return (new actions\Create($this->company_user))->execute();
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function update() : bool
    {
        return (new actions\Update($this->company_user))->execute();
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function delete() : bool
    {
        return (new actions\Delete($this->company_user))->execute();
    }
}