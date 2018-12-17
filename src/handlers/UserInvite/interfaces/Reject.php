<?php

namespace sorokinmedia\user\handlers\UserInvite\interfaces;

/**
 * Interface Reject
 * @package sorokinmedia\user\handlers\UserInvite\interfaces
 */
interface Reject
{
    /**
     * @return bool
     */
    public function reject(): bool;
}