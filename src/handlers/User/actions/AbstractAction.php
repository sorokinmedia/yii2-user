<?php
namespace sorokinmedia\user\handlers\User\actions;

use common\components\user\forms\RegisterForm;
use sorokinmedia\user\handlers\User\interfaces\ActionExecutable;
use sorokinmedia\user\entities\User\UserInterface;

/**
 * Class AbstractAction
 * @package sorokinmedia\user\handlers\User\actions
 *
 * @property UserInterface $user
 * @property RegisterForm $signup_form
 */
abstract class AbstractAction implements ActionExecutable
{
    protected $user;
    protected $signup_form;

    /**
     * AbstractAction constructor.
     * @param UserInterface $user
     * @param RegisterForm|null $signup_form
     */
    public function __construct(UserInterface $user, RegisterForm $signup_form = null)
    {
        $this->user = $user;
        if (!is_null($signup_form)){
            $this->signup_form = $signup_form;
        }
        return $this;
    }
}