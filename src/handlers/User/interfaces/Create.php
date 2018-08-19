<?php
namespace sorokinmedia\user\handlers\User\interfaces;

use common\components\user\forms\RegisterForm;

/**
 * Interface Create
 * @package sorokinmedia\user\handlers\User\interfaces
 */
interface Create
{
    /**
     * @param RegisterForm $signup_form
     * @return bool
     */
    public function create(RegisterForm $signup_form) : bool;
}