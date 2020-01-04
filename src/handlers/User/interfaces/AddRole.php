<?php

namespace sorokinmedia\user\handlers\User\interfaces;

use yii\rbac\Role;

/**
 * Interface AddRole
 * @package sorokinmedia\user\handlers\User\interfaces
 */
interface AddRole
{
    /**
     * @param Role $role
     * @return bool
     */
    public function addRole(Role $role): bool;
}
