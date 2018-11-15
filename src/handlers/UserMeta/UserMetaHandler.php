<?php
namespace sorokinmedia\user\handlers\UserMeta;

use sorokinmedia\user\entities\UserMeta\json\{
    UserMetaFullName,UserMetaPhone
};
use sorokinmedia\user\handlers\UserMeta\interfaces\{Create, SetFullName, SetPhone, Update, VerifyPhone};
use sorokinmedia\user\entities\UserMeta\UserMetaInterface;

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
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function create() : bool
    {
        return (new actions\Create($this->user_meta))->execute();
    }

    /**
     * обновление меты
     * @return bool
     * @throws \yii\db\Exception
     */
    public function update() : bool
    {
        return (new actions\Update($this->user_meta))->execute();
    }

    /**
     * добавление телефона
     * @param UserMetaPhone $userMetaPhone
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function setPhone(UserMetaPhone $userMetaPhone) : bool
    {
        return (new actions\SetPhone($this->user_meta, $userMetaPhone))->execute();
    }

    /**
     * верификация телефона
     * @return bool
     * @throws \yii\db\Exception
     */
    public function verifyPhone() : bool
    {
        return (new actions\VerifyPhone($this->user_meta))->execute();
    }

    /**
     * @param UserMetaFullName $userMetaFullName
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function setFullName(UserMetaFullName $userMetaFullName) : bool
    {
        return (new actions\SetFullName($this->user_meta, $userMetaFullName))->execute();
    }
}