<?php

namespace sorokinmedia\user\handlers\UserInvite\interfaces;

use sorokinmedia\user\forms\InviteForm;

/**
 * Interface Invite
 * @package sorokinmedia\user\handlers\UserInvite\interfaces
 */
interface InviteNewUser
{
    /**
     * @param InviteForm $form
     * @return bool
     */
    public function inviteNewUser(InviteForm $form): bool;
}