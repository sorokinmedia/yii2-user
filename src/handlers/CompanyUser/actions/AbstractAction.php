<?php
namespace sorokinmedia\user\handlers\CompanyUser\actions;

use sorokinmedia\user\handlers\CompanyUser\interfaces\ActionExecutable;
use sorokinmedia\user\entities\CompanyUser\AbstractCompanyUser;

/**
 * Class AbstractAction
 * @package sorokinmedia\user\handlers\CompanyUser\actions
 *
 * @property AbstractCompanyUser $company_user
 */
abstract class AbstractAction implements ActionExecutable
{
    protected $company_user;

    /**
     * AbstractAction constructor.
     * @param AbstractCompanyUser $company_user
     */
    public function __construct(AbstractCompanyUser $company_user)
    {
        $this->company_user = $company_user;
        return $this;
    }
}