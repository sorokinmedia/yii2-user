<?php
namespace sorokinmedia\user\tests\entities\UserMeta;

use sorokinmedia\user\entities\UserMeta\AbstractUserMeta;
use sorokinmedia\user\entities\UserMeta\json\UserMetaPhone;
use sorokinmedia\user\tests\entities\User\RelationClassTrait;
use yii\db\Exception;
use yii\helpers\Json;

/**
 * Class UserMeta
 * @package sorokinmedia\user\tests\entities\UserMeta
 */
class UserMeta extends AbstractUserMeta
{
    use RelationClassTrait;

    /**
     * //TODO: приходится перегружать, т.к. json требует кодирования, декодирования. с MySQL проблем не замечено
     * добавить номер телефона в профиль
     * @param UserMetaPhone $userMetaPhone
     * @return bool
     * @throws Exception
     */
    public function setPhone(UserMetaPhone $userMetaPhone) : bool
    {
        $this->notification_phone = Json::encode($userMetaPhone);
        return $this->updateModel();
    }

    /**
     * верификация номер телефона
     * @return bool
     * @throws Exception
     */
    public function verifyPhone(): bool
    {
        $phone = new UserMetaPhone(Json::decode($this->notification_phone));
        $phone->verifyPhone();
        $this->notification_phone = Json::encode($phone);
        return $this->updateModel();
    }
}