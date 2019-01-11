<?php
namespace sorokinmedia\user\handlers\User\actions;

use sorokinmedia\user\forms\{
    SignupForm,SignUpFormEmail
};
use sorokinmedia\user\handlers\User\interfaces\ActionExecutable;
use sorokinmedia\user\entities\User\UserInterface;

/**
 * Class AbstractAction
 * @package sorokinmedia\user\handlers\User\actions
 *
 * @property UserInterface $user
 * @property SignupForm|SignUpFormEmail $signup_form
 */
abstract class AbstractAction implements ActionExecutable
{
    protected $user;
    protected $signup_form;

    /**
     * AbstractAction constructor.
     * @param UserInterface $user
     * @param SignupForm|null $signup_form
     * @param SignUpFormEmail|null $signup_form_email
     */
    public function __construct(UserInterface $user, SignupForm $signup_form = null, SignUpFormEmail $signup_form_email = null)
    {
        $this->user = $user;
        if ($signup_form !== null){
            $this->signup_form = $signup_form;
        }
        if ($signup_form_email !== null){
            $this->signup_form = $signup_form_email;
        }
        return $this;
    }
}