<?php
namespace sorokinmedia\user\handlers\User\actions;

use sorokinmedia\user\entities\User\UserInterface;
use yii\rbac\Role;

/**
 * Class RevokeRole
 * @package sorokinmedia\user\handlers\User\actions
 *
 * @property Role $role
 */
class RevokeRole extends AbstractAction
{
    public $role;

    /**
     * RevokeRole constructor.
     * @param UserInterface $user
     * @param Role $role
     */
    public function __construct(UserInterface $user, Role $role)
    {
        $this->role = $role;
        parent::__construct($user);
    }

    /**
     * @return bool
     */
    public function execute() : bool
    {
        $this->user->downgradeFromRole($this->role);
        return true;
    }
}