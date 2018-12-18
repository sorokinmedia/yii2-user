<?php

namespace sorokinmedia\user\handlers\UserInvite\interfaces;

use sorokinmedia\user\entities\UserInvite\AbstractUserInvite;

/**
 * Interface Reject
 * @package sorokinmedia\user\handlers\UserInvite\interfaces
 */
interface Reject
{
    /**
     * @param AbstractUserInvite $invite
     * @return bool
     */
    public function reject(AbstractUserInvite $invite): bool;
}