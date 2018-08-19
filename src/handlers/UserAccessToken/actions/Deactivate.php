<?php
namespace sorokinmedia\user\handlers\UserAccessToken\actions;

use sorokinmedia\user\entities\UserAccessToken\AbstractUserAccessToken;

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
     * @throws \yii\db\Exception
     */
    public function execute() : bool
    {
        $this->token->deactivate();
        return true;
    }
}