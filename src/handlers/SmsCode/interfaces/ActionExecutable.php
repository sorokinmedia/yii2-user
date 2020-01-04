<?php

namespace sorokinmedia\user\handlers\SmsCode\interfaces;

/**
 * Interface ActionExecutable
 * @package sorokinmedia\user\handlers\SmsCode\interfaces
 */
interface ActionExecutable
{
    /**
     * @return mixed
     */
    public function execute(): bool;
}
