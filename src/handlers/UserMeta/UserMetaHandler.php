<?php

namespace sorokinmedia\user\handlers\UserMeta;

use Throwable;
use sorokinmedia\user\entities\UserMeta\json\{UserMetaFullName, UserMetaPhone};
use sorokinmedia\user\entities\UserMeta\UserMetaInterface;
use sorokinmedia\user\handlers\UserMeta\interfaces\{Create, SetFullName, SetPhone, Update, VerifyPhone};
use yii\db\Exception;

/**
 * Class UserMetaHandler
 * @package sorokinmedia\user\handlers\UserMeta
 *
 * @property UserMetaInterface $user_meta
 */
class UserMetaHandler implements Create, Update, SetPhone, VerifyPhone, SetFullName
{
    public $user_meta;

    /**
     * UserMetaHandler constructor.
     * @param UserMetaInterface $userMeta
     */
    public function __construct(UserMetaInterface $userMeta)
    {
        $this->user_meta = $userMeta;
        return $this;
    }

    /**
     * создание меты
     * @return bool
     * @throws Throwable
     * @throws Exception
     */
    public function create(): bool
    {
        return (new actions\Create($this->user_meta))->execute();
    }

    /**
     * обновление меты
     * @return bool
     * @throws Exception
     */
    public function update(): bool
    {
        return (new actions\Update($this->user_meta))->execute();
    }

    /**
     * добавление телефона
     * @param UserMetaPhone $userMetaPhone
     * @return bool
     * @throws Throwable
     * @throws Exception
     */
    public function setPhone(UserMetaPhone $userMetaPhone): bool
    {
        return (new actions\SetPhone($this->user_meta, $userMetaPhone))->execute();
    }

    /**
     * верификация телефона
     * @return bool
     */
    public function verifyPhone(): bool
    {
        return (new actions\VerifyPhone($this->user_meta))->execute();
    }

    /**
     * @param UserMetaFullName $userMetaFullName
     * @return bool
     * @throws Throwable
     * @throws Exception
     */
    public function setFullName(UserMetaFullName $userMetaFullName): bool
    {
        return (new actions\SetFullName($this->user_meta, $userMetaFullName))->execute();
    }
}
