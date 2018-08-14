<?php
namespace sorokinmedia\user\handlers\UserMeta\interfaces;

/**
 * Interface ActionExecutable
 * @package sorokinmedia\user\handlers\UserMeta\interfaces
 */
interface ActionExecutable
{
    /**
     * @return mixed
     */
    public function execute() : bool;
}