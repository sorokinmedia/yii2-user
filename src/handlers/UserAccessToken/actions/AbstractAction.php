<?php
namespace sorokinmedia\user\handlers\UserAccessToken\actions;

use sorokinmedia\user\handlers\UserAccessToken\interfaces\ActionExecutable;
use sorokinmedia\user\entities\UserAccessToken\UserAccessTokenInterface;

/**
 * Class AbstractAction
 * @package sorokinmedia\user\handlers\UserAccessToken\actions
 *
 * @property UserAccessTokenInterface $token
 */
abstract class AbstractAction implements ActionExecutable
{
    protected $token;

    /**
     * AbstractAction constructor.
     * @param UserAccessTokenInterface $token
     */
    public function __construct(UserAccessTokenInterface $token)
    {
        $this->token = $token;
        return $this;
    }
}