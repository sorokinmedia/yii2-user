<?php
namespace sorokinmedia\user\handlers\SmsCode\actions;

use sorokinmedia\user\entities\SmsCode\AbstractSmsCode;

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
     * @throws \yii\db\Exception
     */
    public function execute() : bool
    {
        $this->sms_code->updateModel();
        return true;
    }
}