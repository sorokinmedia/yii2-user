<?php
namespace sorokinmedia\user\handlers\User\interfaces;

/**
 * Interface Unblock
 * @package sorokinmedia\user\handlers\User\interfaces
 */
interface Unblock
{
    /**
     * @return bool
     */
    public function unblock() : bool;
}