<?php

namespace sorokinmedia\user\handlers\UserMeta\interfaces;

use sorokinmedia\user\entities\UserMeta\json\UserMetaPhone;

/**
 * Interface SetPhone
 * @package sorokinmedia\user\handlers\UserMeta\interfaces
 */
interface SetPhone
{
    /**
     * @param UserMetaPhone $userMetaPhone
     * @return bool
     */
    public function setPhone(UserMetaPhone $userMetaPhone): bool;
}
