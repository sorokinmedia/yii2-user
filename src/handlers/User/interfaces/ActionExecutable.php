<?php

namespace sorokinmedia\user\handlers\User\interfaces;

/**
 * Interface ActionExecutable
 * @package sorokinmedia\user\handlers\User\interfaces
 */
interface ActionExecutable
{
    /**
     * @return mixed
     */
    public function execute(): bool;
}
