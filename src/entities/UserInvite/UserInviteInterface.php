<?php

namespace sorokinmedia\user\entities\UserInvite;

use yii\db\ActiveQueryInterface;

/**
 * Interface UserInviteInterface
 * @package sorokinmedia\user\entities\Company
 */
interface UserInviteInterface
{
    /**
     * @return ActiveQueryInterface
     */
    public function getInitiator(): ActiveQueryInterface;

    /**
     * @return ActiveQueryInterface
     */
    public function getUser(): ActiveQueryInterface;

    /**
     * @return ActiveQueryInterface
     */
    public function getCompany(): ActiveQueryInterface;

    /**
     * @return bool
     */
    public function sendNotificationsToNewUser(): bool;

    /**
     * @return bool
     */
    public function sendNotificationsToExistingUser(): bool;

    /**
     * @return bool
     */
    public function accept(): bool;

    /**
     * @return bool
     */
    public function reject(): bool;

}