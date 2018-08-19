<?php
namespace sorokinmedia\user\handlers\UserAccessToken\interfaces;

/**
 * Interface Create
 * @package sorokinmedia\user\handlers\UserAccessToken\interfaces
 */
interface Create
{
    /**
     * @return bool
     */
    public function create() : bool;
}