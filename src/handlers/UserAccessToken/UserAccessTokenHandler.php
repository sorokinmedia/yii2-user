<?php
namespace sorokinmedia\user\handlers\UserAccessToken;

use sorokinmedia\user\handlers\UserAccessToken\interfaces\{Create, Deactivate};
use sorokinmedia\user\entities\UserAccessToken\UserAccessTokenInterface;

/**
 * Class UserAccessTokenHandler
 * @package sorokinmedia\user\handlers\UserAccessToken
 *
 * @property UserAccessTokenInterface $token
 */
class UserAccessTokenHandler implements Create, Deactivate
{
    public $token;

    /**
     * UserAccessTokenHandler constructor.
     * @param UserAccessTokenInterface $token
     */
    public function __construct(UserAccessTokenInterface $token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function create() : bool
    {
        return (new actions\Create($this->token))->execute();
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function deactivate() : bool
    {
        return (new actions\Deactivate($this->token))->execute();
    }
}