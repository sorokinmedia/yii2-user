<?php

namespace sorokinmedia\user\handlers\SmsCode\actions;

use sorokinmedia\user\entities\SmsCode\AbstractSmsCode;
use yii\db\Exception;

/**
 * Class Update
 * @package sorokinmedia\user\handlers\SmsCode\actions
 *
 * @property AbstractSmsCode $sms_code
 */
class Update extends AbstractAction
{
    /**
     * @return bool
     * @throws Exception
     */
    public function execute(): bool
    {
        $this->sms_code->updateModel();
        return true;
    }
}
