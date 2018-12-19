<?php
namespace sorokinmedia\user\handlers\UserInvite\interfaces;

/**
 * Interface ActionExecutable
 * @package sorokinmedia\user\handlers\UserInvite\interfaces
 */
interface ActionExecutable
{
    /**
     * @return bool
     */
    public function execute() : bool;
}