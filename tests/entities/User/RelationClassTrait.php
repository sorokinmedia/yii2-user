<?php
namespace sorokinmedia\user\tests\entities\User;

use sorokinmedia\user\tests\entities\SmsCode\SmsCode;
use sorokinmedia\user\tests\entities\UserAccessToken\UserAccessToken;
use sorokinmedia\user\tests\entities\UserMeta\UserMeta;

trait RelationClassTrait
{
    public $__userClass;
    public $__userMetaClass;
    public $__userAccessTokenClass;
    public $__smsCodeClass;

    public function initClasses()
    {
        $this->__userClass = User::class;
        $this->__userMetaClass = UserMeta::class;
        $this->__userAccessTokenClass = UserAccessToken::class;
        $this->__smsCodeClass = SmsCode::class;
    }

    /**
     * инициализация связей
     */
    public function init()
    {
        parent::init();
        $this->initClasses();
    }

    /**
     * метод для динамической подстановки нужного класса в связь
     * @param string $field
     * @param string $class
     * @return mixed
     */
    public function setRelationClass(string $field, string $class)
    {
        return $this->{$field} = $class;
    }
}