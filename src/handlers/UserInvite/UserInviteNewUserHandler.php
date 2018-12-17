<?php
namespace sorokinmedia\user\handlers\UserInvite;

use sorokinmedia\user\forms\InviteForm;
use sorokinmedia\user\handlers\UserInvite\interfaces\{Accept, InviteExistingUser, InviteNewUser, Reject};

/**
 * Class UserInviteHandler
 * @package sorokinmedia\user\handlers\UserInvite
 */
class UserInviteNewUserHandler implements Accept, Reject, InviteNewUser, InviteExistingUser
{
    protected $company;
    protected $owner;
    protected $role;

    /**
     * @return bool
     */
    public function accept() : bool
    {
        return (new actions\Accept())->execute();
    }

    /**
     * @return bool
     */
    public function reject() : bool
    {
        return (new actions\Accept())->execute();
    }

    /**
     * @param InviteForm $form
     * @return bool
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function inviteExistingUser(InviteForm $form): bool
    {
        return (new actions\InviteExistingUser($form))->execute();
    }

    /**
     * @param InviteForm $form
     * @return bool
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     */
    public function inviteNewUser(InviteForm $form): bool
    {
        return (new actions\InviteNewUser($form))->execute();
    }


}