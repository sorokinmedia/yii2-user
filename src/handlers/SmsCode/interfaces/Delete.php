<?php
namespace sorokinmedia\user\handlers\SmsCode\interfaces;

/**
 * Interface Delete
 * @package sorokinmedia\user\handlers\SmsCode\interfaces
 */
interface Delete
{
    /**
     * @return bool
     */
    public function delete() : bool;
}