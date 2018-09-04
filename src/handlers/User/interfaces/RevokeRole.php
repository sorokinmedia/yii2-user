<?php
namespace sorokinmedia\user\handlers\User\interfaces;

use yii\rbac\Role;

/**
 * Interface RevokeRole
 * @package sorokinmedia\user\handlers\User\interfaces
 */
interface RevokeRole
{
    /**
     * @param Role $role
     * @return bool
     */
    public function revokeRole(Role $role) : bool;
}