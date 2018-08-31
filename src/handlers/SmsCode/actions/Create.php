<?php
namespace sorokinmedia\user\handlers\SmsCode\actions;

use sorokinmedia\user\entities\SmsCode\AbstractSmsCode;

/**
 * Class Create
 * @package sorokinmedia\user\handlers\SmsCode\actions
 *
 * @property AbstractSmsCode $sms_code
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
        $this->sms_code->insertModel();
        $this->sms_code->refresh();
        $this->sms_code->sendCode();
        return true;
    }
}