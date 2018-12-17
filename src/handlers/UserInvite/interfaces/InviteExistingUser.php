<?php

namespace sorokinmedia\user\handlers\UserInvite\interfaces;

use sorokinmedia\user\forms\InviteForm;

/**
 * Interface Invite
 * @package sorokinmedia\user\handlers\UserInvite\interfaces
 */
interface InviteExistingUser
{
    /**
     * @param InviteForm $form
     * @return bool
     */
    public function inviteExistingUser(InviteForm $form): bool;
}