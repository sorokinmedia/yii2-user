<?php
namespace sorokinmedia\user\handlers\UserMeta\interfaces;

use sorokinmedia\user\entities\UserMeta\json\UserMetaFullName;

/**
 * Interface SetPhone
 * @package sorokinmedia\user\handlers\UserMeta\interfaces
 */
interface SetFullName
{
    /**
     * @param UserMetaFullName $userMetaFullName
     * @return bool
     */
    public function setFullName(UserMetaFullName $userMetaFullName) : bool;
}