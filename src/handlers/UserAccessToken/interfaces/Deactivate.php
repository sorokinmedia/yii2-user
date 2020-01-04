<?php

namespace sorokinmedia\user\handlers\UserAccessToken\interfaces;

/**
 * Interface Deactivate
 * @package sorokinmedia\user\handlers\UserAccessToken\interfaces
 */
interface Deactivate
{
    /**
     * @return bool
     */
    public function deactivate(): bool;
}
