<?php
namespace sorokinmedia\user\handlers\UserMeta\interfaces;

/**
 * Interface VerifyPhone
 * @package sorokinmedia\user\handlers\UserMeta\interfaces
 */
interface VerifyPhone
{
    /**
     * @return bool
     */
    public function verifyPhone() : bool;
}