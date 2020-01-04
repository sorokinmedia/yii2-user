<?php

namespace sorokinmedia\user\handlers\CompanyUser\interfaces;

use sorokinmedia\user\entities\CompanyUser\AbstractCompanyUserPermission;

/**
 * Interface RemovePermission
 * @package sorokinmedia\user\handlers\CompanyUser\interfaces
 */
interface RemovePermission
{
    /**
     * @param AbstractCompanyUserPermission $permission
     * @return bool
     */
    public function removePermission(AbstractCompanyUserPermission $permission): bool;
}
