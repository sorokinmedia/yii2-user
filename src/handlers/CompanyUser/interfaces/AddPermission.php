<?php
namespace sorokinmedia\user\handlers\CompanyUser\interfaces;

use sorokinmedia\user\entities\CompanyUser\AbstractCompanyUserPermission;

/**
 * Interface AddPermission
 * @package sorokinmedia\user\handlers\CompanyUser\interfaces
 */
interface AddPermission
{
    /**
     * @param AbstractCompanyUserPermission $permission
     * @return bool
     */
    public function addPermission(AbstractCompanyUserPermission $permission) : bool;
}