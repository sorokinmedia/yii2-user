<?php

namespace sorokinmedia\user\handlers\UserAccessToken\actions;

use sorokinmedia\user\entities\UserAccessToken\AbstractUserAccessToken;
use Throwable;
use yii\db\Exception;

/**
 * Class Create
 * @package sorokinmedia\user\handlers\UserAccessToken\actions
 *
 * @property AbstractUserAccessToken $token
 */
class Create extends AbstractAction
{
    /**
     * @return bool
     * @throws Throwable
     * @throws Exception
     */
    public function execute(): bool
    {
        $this->token->insertModel();
        return true;
    }
}
