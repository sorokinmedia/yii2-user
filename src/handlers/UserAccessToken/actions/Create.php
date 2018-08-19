<?php
namespace sorokinmedia\user\handlers\UserAccessToken\actions;

use sorokinmedia\user\entities\UserAccessToken\AbstractUserAccessToken;

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
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function execute() : bool
    {
        $this->token->insertModel();
        return true;
    }
}