<?php
namespace sorokinmedia\user\handlers\UserInvite\actions;


use sorokinmedia\user\entities\UserInvite\AbstractUserInvite;

/**
 * Class Reject
 * @package sorokinmedia\user\handlers\UserInvite\actions
 */
class Reject extends AbstractAction
{
    /**
     * @var AbstractUserInvite
     */
    private $invite;

    /**
     * Accept constructor.
     * @param AbstractUserInvite $invite
     */
    public function __construct(AbstractUserInvite $invite)
    {
        $this->invite = $invite;
    }

    /**
     * @return bool
     */
    public function execute(): bool
    {
        return $this->invite->reject();
    }
}