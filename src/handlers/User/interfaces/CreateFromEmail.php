<?php

namespace sorokinmedia\user\handlers\User\interfaces;

use sorokinmedia\user\forms\SignUpFormEmail;

/**
 * Interface CreateFromEmail
 * @package sorokinmedia\user\handlers\User\interfaces
 */
interface CreateFromEmail
{
    /**
     * @param SignUpFormEmail $signUpFormEmail
     * @return bool
     */
    public function createFromEmail(SignUpFormEmail $signUpFormEmail): bool;
}
