<?php

namespace sorokinmedia\user\handlers\SmsCode\actions;

use Exception;
use sorokinmedia\user\entities\SmsCode\AbstractSmsCode;

/**
 * Class Delete
 * @package sorokinmedia\user\handlers\SmsCode\actions
 *
 * @property AbstractSmsCode $sms_code
 */
class Delete extends AbstractAction
{
    /**
     * @return bool
     * @throws Exception
     */
    public function execute(): bool
    {
        $this->sms_code->deleteModel();
        return true;
    }
}
