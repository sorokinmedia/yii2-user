<?php

namespace sorokinmedia\user\handlers\User\interfaces;

use sorokinmedia\user\forms\SignupForm;

/**
 * Interface Create
 * @package sorokinmedia\user\handlers\User\interfaces
 */
interface Create
{
    /**
     * @param SignupForm $signup_form
     * @return bool
     */
    public function create(SignupForm $signup_form): bool;
}
