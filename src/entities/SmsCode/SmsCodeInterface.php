<?php
namespace sorokinmedia\user\entities\SmsCode;

use sorokinmedia\user\entities\User\AbstractUser;

/**
 * Interface SmsCodeInterface
 * @package sorokinmedia\user\entities\SmsCode
 */
interface SmsCodeInterface
{
    /**
     * список типов или текстовка
     * @param int|null $type_id
     * @return mixed
     */
    public static function getTypes(int $type_id = null);

    /**
     * текстовка типа
     * @return string
     */
    public function getType() : string;

    /**
     * получить пользователя
     * @return mixed
     */
    public function getUser();

    /**
     * сгенерировать числовой код
     * @return int
     */
    public function generateCode() : int;

    /**
     * получить код для юзера
     * @param AbstractUser $user
     * @param int $type_id
     * @return mixed
     */
    public static function getCodeByUser(AbstractUser $user, int $type_id);

    /**
     * получить код по IP юзера
     * @param string $ip
     * @param int $type_id
     * @return mixed
     */
    public static function getCodeByIp(string $ip, int $type_id);

    /**
     * получить кол-во кодов запрошенных с одного IP за 24 часа
     * @param string $ip
     * @param int $type_id
     * @return mixed
     */
    public static function getRequestedTodayByIp(string $ip, int $type_id);

    /**
     * получить кол-во кодов запрошенных с одного пользователя за 24 часа
     * @param AbstractUser $user
     * @param int $type_id
     * @return mixed
     */
    public static function getRequestedTodayByUser(AbstractUser $user, int $type_id);

    /**
     * получить все коды запрошенные пользователем за 24 часа
     * @param AbstractUser $user
     * @return mixed
     */
    public static function getRequestedTodayForUser(AbstractUser $user);

    /**
     * добавление модели в БД
     * @return bool
     */
    public function inserModel() : bool;

    /**
     * обновление модели в БД
     * @return bool
     */
    public function updateModel() : bool;

    /**
     * удаление модели из БД
     * @return bool
     */
    public function deleteModel() : bool;
}