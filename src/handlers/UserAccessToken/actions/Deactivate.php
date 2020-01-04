<?php

namespace sorokinmedia\user\handlers\UserAccessToken\actions;

use sorokinmedia\user\entities\UserAccessToken\AbstractUserAccessToken;
use yii\db\Exception;

/**
 * Class Deactivate
 * @package sorokinmedia\user\handlers\UserAccessToken\actions
 *
 * @property AbstractUserAccessToken $token
 */
class Deactivate extends AbstractAction
{
    /**
     * @return bool
     * @throws Exception
     */
    public function execute(): bool
    {
        $this->token->deactivate();
        return true;
    }
}
