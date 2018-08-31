<?php
namespace sorokinmedia\user\handlers\SmsCode;

use sorokinmedia\user\handlers\SmsCode\interfaces\{Create, Delete, Update};
use sorokinmedia\user\entities\SmsCode\SmsCodeInterface;

/**
 * Class SmsCodeHandler
 * @package sorokinmedia\user\handlers\SmsCode
 *
 * @property SmsCodeInterface $sms_code
 */
class SmsCodeHandler implements Create, Update, Delete
{
    public $sms_code;

    /**
     * SmsCodeHandler constructor.
     * @param SmsCodeInterface $sms_code
     */
    public function __construct(SmsCodeInterface $sms_code)
    {
        $this->sms_code = $sms_code;
        return $this;
    }

    /**
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function create() : bool
    {
        return (new actions\Create($this->sms_code))->execute();
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function update() : bool
    {
        return (new actions\Update($this->sms_code))->execute();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function delete() : bool
    {
        return (new actions\Delete($this->sms_code))->execute();
    }
}