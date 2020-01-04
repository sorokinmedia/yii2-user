<?php

namespace sorokinmedia\user\handlers\UserAccessToken\interfaces;

/**
 * Interface ActionExecutable
 * @package sorokinmedia\user\handlers\UserAccessToken\interfaces
 */
interface ActionExecutable
{
    /**
     * @return mixed
     */
    public function execute(): bool;
}
