<?php

namespace sorokinmedia\user\handlers\SmsCode\actions;

use sorokinmedia\user\entities\SmsCode\SmsCodeInterface;
use sorokinmedia\user\handlers\SmsCode\interfaces\ActionExecutable;

/**
 * Class AbstractAction
 * @package sorokinmedia\user\handlers\SmsCode\actions
 *
 * @property SmsCodeInterface $sms_code
 */
abstract class AbstractAction implements ActionExecutable
{
    protected $sms_code;

    /**
     * AbstractAction constructor.
     * @param SmsCodeInterface $sms_code
     */
    public function __construct(SmsCodeInterface $sms_code)
    {
        $this->sms_code = $sms_code;
        return $this;
    }
}
