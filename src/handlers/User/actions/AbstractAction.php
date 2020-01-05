<?php

namespace sorokinmedia\user\handlers\User\actions;

use sorokinmedia\user\entities\User\UserInterface;
use sorokinmedia\user\forms\{SignupForm, SignUpFormConsole, SignUpFormEmail, SignUpFormExisted};
use sorokinmedia\user\handlers\User\interfaces\ActionExecutable;

/**
 * Class AbstractAction
 * @package sorokinmedia\user\handlers\User\actions
 *
 * @property UserInterface $user
 * @property SignupForm|SignUpFormEmail|SignUpFormConsole $signup_form
 */
abstract class AbstractAction implements ActionExecutable
{
    protected UserInterface $user;
    protected $signup_form;

    /**
     * AbstractAction constructor.
     * @param UserInterface $user
     * @param SignupForm|null $sign_up_form
     * @param SignUpFormEmail|null $sign_up_form_email
     * @param SignUpFormConsole|null $sign_up_form_console
     * @param SignUpFormExisted|null $sign_up_form_existed
     */
    public function __construct(
        UserInterface $user,
        SignupForm $sign_up_form = null,
        SignUpFormEmail $sign_up_form_email = null,
        SignUpFormConsole $sign_up_form_console = null,
        SignUpFormExisted $sign_up_form_existed = null)
    {
        $this->user = $user;
        if ($sign_up_form !== null) {
            $this->signup_form = $sign_up_form;
        }
        if ($sign_up_form_email !== null) {
            $this->signup_form = $sign_up_form_email;
        }
        if ($sign_up_form_console !== null) {
            $this->signup_form = $sign_up_form_console;
        }
        if ($sign_up_form_existed !== null) {
            $this->signup_form = $sign_up_form_existed;
        }
        return $this;
    }
}
