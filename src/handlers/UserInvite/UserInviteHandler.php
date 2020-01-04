<?php

namespace sorokinmedia\user\handlers\UserInvite;

use sorokinmedia\user\entities\UserInvite\AbstractUserInvite;
use sorokinmedia\user\forms\InviteForm;
use sorokinmedia\user\handlers\UserInvite\interfaces\{Accept, InviteExistingUser, InviteNewUser, Reject};
use yii\db\Exception;

/**
 * Class UserInviteHandler
 * @package sorokinmedia\user\handlers\UserInvite
 */
class UserInviteHandler implements Accept, Reject, InviteNewUser, InviteExistingUser
{
    /**
     * @param AbstractUserInvite $invite
     * @return bool
     */
    public function accept(AbstractUserInvite $invite): bool
    {
        return (new actions\Accept($invite))->execute();
    }

    /**
     * @param AbstractUserInvite $invite
     * @return bool
     */
    public function reject(AbstractUserInvite $invite): bool
    {
        return (new actions\Reject($invite))->execute();
    }

    /**
     * @param InviteForm $form
     * @return bool
     * @throws \yii\base\Exception
     * @throws Exception
     */
    public function inviteExistingUser(InviteForm $form): bool
    {
        return (new actions\InviteExistingUser($form))->execute();
    }

    /**
     * @param InviteForm $form
     * @return bool
     * @throws \yii\base\Exception
     * @throws Exception
     */
    public function inviteNewUser(InviteForm $form): bool
    {
        return (new actions\InviteNewUser($form))->execute();
    }


}
