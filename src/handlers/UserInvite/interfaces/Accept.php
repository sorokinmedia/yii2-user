<?php

namespace sorokinmedia\user\handlers\UserInvite\interfaces;

/**
 * Interface Accept
 * @package sorokinmedia\user\handlers\UserInvite\interfaces
 */
interface Accept
{
    /**
     * @return bool
     */
    public function accept(): bool;
}