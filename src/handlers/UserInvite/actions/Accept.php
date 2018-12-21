<?php

namespace sorokinmedia\user\handlers\UserInvite\actions;


use sorokinmedia\user\entities\UserInvite\AbstractUserInvite;

/**
 * Class Accept
 * @package sorokinmedia\user\handlers\UserInvite\actions
 */
class Accept extends AbstractAction
{
    /**
     * @var AbstractUserInvite
     */
    protected $invite;

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
        return $this->invite->accept();
    }
}