<?php

namespace sorokinmedia\user\handlers\User\interfaces;

use sorokinmedia\user\forms\SignUpFormConsole;

/**
 * Interface CreateFromConsole
 * @package sorokinmedia\user\handlers\User\interfaces
 */
interface CreateFromConsole
{
    /**
     * @param SignUpFormConsole $signup_form
     * @return bool
     */
    public function createFromConsole(SignUpFormConsole $signup_form): bool;
}
