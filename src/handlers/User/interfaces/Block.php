<?php
namespace sorokinmedia\user\handlers\User\interfaces;

/**
 * Interface Block
 * @package sorokinmedia\user\handlers\User\interfaces
 */
interface Block
{
    /**
     * @return bool
     */
    public function block() : bool;
}