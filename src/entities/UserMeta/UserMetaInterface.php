<?php
namespace sorokinmedia\user\entities\UserMeta;

use sorokinmedia\user\entities\User\UserInterface;

/**
 * Interface UserMetaInterface
 * @package sorokinmedia\user\entities\UserMeta
 */
interface UserMetaInterface
{
    /**
     * получить пользователя
     * @return mixed
     */
    public function getUser();

    /**
     * создание модели в БД
     * @return bool
     */
    public function insertModel() : bool;

    /**
     * обновление модели в БД
     * @return bool
     */
    public function updateModel() : bool;

    /**
     * трансфер данных из формы в модель
     * @return mixed
     */
    public function getFromForm();

    /**
     * статический конструктор. создает новую модель в БД или возвращает существующую по user->id
     * @param UserInterface $user
     * @return UserMetaInterface
     */
    public static function create(UserInterface $user) : UserMetaInterface;

    /**
     * верификация телефона
     * @return bool
     */
    public function verifyPhone() : bool;
}