<?php
namespace sorokinmedia\user\tests\entities\User;

trait RelationClassTrait
{
    public $__userClass;
    public $__userMetaClass;
    public $__userAccessTokenClass;

    public function initClasses()
    {
        $this->__userClass = User::class;
        $this->__userMetaClass = UserMeta::class;
        $this->__userAccessTokenClass = UserAccessToken::class;
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