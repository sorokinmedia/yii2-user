<?php

namespace sorokinmedia\user\handlers\UserInvite\interfaces;

use sorokinmedia\user\entities\UserInvite\AbstractUserInvite;

/**
 * Interface Accept
 * @package sorokinmedia\user\handlers\UserInvite\interfaces
 */
interface Accept
{
    /**
     * @param AbstractUserInvite $invite
     * @return bool
     */
    public function accept(AbstractUserInvite $invite): bool;
}