<?php
namespace sorokinmedia\user\entities\SmsCode;

use sorokinmedia\user\entities\User\AbstractUser;
use sorokinmedia\user\entities\UserMeta\json\UserMetaPhone;

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
     * перенос данных из формы в модель
     * @return mixed
     */
    public function getFromForm();

    /**
     * добавление модели в БД
     * @return bool
     */
    public function insertModel() : bool;

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

    /**
     * сформировать текст сообщения исходя из типа кода
     * @return string
     */
    public function getMessage() : string;

    /**
     * отправка кода
     * @return bool
     */
    public function sendCode() : bool;

    /**
     * сброс дневного лимита для пользователя
     * @param AbstractUser $user
     * @return bool
     */
    public static function resetLimit(AbstractUser $user) : bool;

    /**
     * отметить как использованный
     * @param bool $is_validated
     * @return bool
     */
    public function checkUse(bool $is_validated) : bool;

    /**
     * форматтер для номера телефона. пока только россия
     * @param UserMetaPhone $userMetaPhone
     * @return string
     */
    public static function phoneFormatter(UserMetaPhone $userMetaPhone) : string;
}