<?php

namespace sorokinmedia\user\entities\UserMeta;

use sorokinmedia\user\entities\User\UserInterface;
use sorokinmedia\user\entities\UserMeta\json\UserMetaFullName;
use sorokinmedia\user\entities\UserMeta\json\UserMetaPhone;
use yii\db\ActiveQuery;

/**
 * Interface UserMetaInterface
 * @package sorokinmedia\user\entities\UserMeta
 */
interface UserMetaInterface
{
    /**
     * статический конструктор. создает новую модель в БД или возвращает существующую по user->id
     * @param UserInterface $user
     * @return UserMetaInterface
     */
    public static function create(UserInterface $user): UserMetaInterface;

    /**
     * получить пользователя
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery;

    /**
     * создание модели в БД
     * @return bool
     */
    public function insertModel(): bool;

    /**
     * обновление модели в БД
     * @return bool
     */
    public function updateModel(): bool;

    /**
     * трансфер данных из формы в модель
     * @return void
     */
    public function getFromForm(): void;

    /**
     * установить номер телефона
     * @param UserMetaPhone $userMetaPhone
     * @return bool
     */
    public function setPhone(UserMetaPhone $userMetaPhone): bool;

    /**
     * верификация телефона
     * @return bool
     */
    public function verifyPhone(): bool;

    /**
     * установить полное имя
     * @param UserMetaFullName $userMetaFullName
     * @return bool
     */
    public function setFullName(UserMetaFullName $userMetaFullName): bool;

    /**
     * отдает варианты отображаемоего имени
     * @return array
     */
    public function getDisplayNameVariants(): array;

    /**
     * отдает варианты для выбора отображаемого имени, ассоциативный массив
     * @return array
     */
    public function getDisplayNameVariantsArray(): array;
}
