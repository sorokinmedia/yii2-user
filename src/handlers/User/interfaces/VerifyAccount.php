<?php

namespace sorokinmedia\user\handlers\User\interfaces;

/**
 * Interface VerifyAccount
 * @package sorokinmedia\user\handlers\User\interfaces
 */
interface VerifyAccount
{
    /**
     * @return bool
     */
    public function verifyAccount(): bool;
}
