<?php
namespace sorokinmedia\user\entities\User;

use sorokinmedia\user\forms\SignupForm;
use yii\rbac\Role;

/**
 * Interface UserInterface
 * @package sorokinmedia\user\entities\User
 */
interface UserInterface
{
    /******************************************************************************************************************
     * СТАТУСЫ
     *****************************************************************************************************************/

    /**
     * список статусов
     * @return array
     */
    public static function getStatusesArray() : array;

    /**
     * текстовое обозначение статуса
     * @return string
     */
    public function getStatus() : string;

    /**
     * смена статуса на активный
     * @return void
     */
    public function activate();

    /**
     * смена статуса на заблокированный
     * @return void
     */
    public function deactivate();

    /**
     * блокировака юзера
     * @return bool
     */
    public function blockUser() : bool;

    /**
     * разблокировка юзера
     * @return bool
     */
    public function unblockUser() : bool;

    /**
     * получает объект роли по ее названию
     * @param string $role_name
     * @return null|Role
     */
    public static function getRole(string $role_name);

    /******************************************************************************************************************
     * СБРОС ПАРОЛЯ
     *****************************************************************************************************************/

    /**
     * генерация токена сброса пароля
     * @return mixed
     */
    public function generatePasswordResetToken();

    /**
     * сохранить токен для сброса пароля
     * @return bool
     */
    public function saveGeneratedPasswordResetToken() : bool;

    /**
     * поиск пользователя по токену сброса пароля
     * @param int $expired
     * @param string $token
     * @return mixed
     */
    public static function findByPasswordResetToken(int $expired, string $token = null);

    /**
     * проверка валидности токена сброса пароля
     * @param int $expired
     * @param string $token
     * @return bool
     */
    public static function isPasswordResetTokenValid(int $expired, string $token = null) : bool;

    /**
     * удаление токена сброса пароля
     * @return mixed
     */
    public function removePasswordResetToken();

    /******************************************************************************************************************
     * ПОДТВЕРЖДЕНИЕ E-MAIL
     *****************************************************************************************************************/

    /**
     * генерация токена подтверждения e-mail
     * @return mixed
     */
    public function generateEmailConfirmToken();

    /**
     * поиск пользователя по токену подтверждения e-mail
     * @param string $email_confirm_token
     * @return mixed
     */
    public static function findByEmailConfirmToken(string $email_confirm_token);

    /**
     * удаление токена подтверждения e-mail
     * @return mixed
     */
    public function removeEmailConfirmToken();

    /**
     * действия при подтверждении e-mail
     * @return bool
     */
    public function confirmEmailAction() : bool;

    /******************************************************************************************************************
     * ПОИСК ПОЛЬЗОВАТЕЛЯ
     *****************************************************************************************************************/

    /**
     * поиск пользователя по e-mail
     * @param string $email
     * @return mixed
     */
    public static function findByEmail(string $email);

    /**
     * поиск пользователей по роли
     * @param string $role
     * @return array
     */
    public static function findByRole(string $role) : array;

    /******************************************************************************************************************
     * РАБОТА С ПАРОЛЕМ
     *****************************************************************************************************************/

    /**
     * валидация пароля
     * @param string $password
     * @return bool
     */
    public function validatePassword(string $password) : bool;

    /**
     * сохранение пароля
     * @param string $password
     * @return mixed
     */
    public function setPassword(string $password);

    /**
     * сохранение нового пароля
     * @param string $password
     * @param bool $reset_token
     * @return bool
     */
    public function saveNewPassword(string $password, bool $reset_token = false) : bool;

    /**
     * генерация API ключа
     * @return mixed
     */
    public function generateAuthKey();

    /******************************************************************************************************************
     * РАБОТА С РОЛЯМИ
     *****************************************************************************************************************/

    /**
     * добавить указанную роль
     * @param Role $role
     * @return bool
     */
    public function upgradeToRole(Role $role) : bool;

    /**
     * удалить указанную роль
     * @param Role $role
     * @return bool
     */
    public function downgradeFromRole(Role $role) : bool;

    /******************************************************************************************************************
     * РАБОТА С ТОКЕНАМИ
     *****************************************************************************************************************/

    /**
     * получить все токены пользователя
     * @return mixed
     */
    public function getTokens();

    /**
     * получает токен из кук
     * @return string
     */
    public function getToken() : string;

    /**
     * деактивировать все токены пользователя
     * @return bool
     */
    public function deactivateTokens() : bool;

    /**
     * проставляет куку и токен после логина
     * @return bool
     */
    public function afterLogin() : bool;

    /**
     * деактиваирует токен и удаляет куку при логауте
     * @return bool
     */
    public function afterLogout() : bool;

    /**
     * получает токен пользователя, под которым нужно войти
     * @return string
     */
    public function getCheckToken() : string;

    /**
     * заменяет токен при заходе под другим пользователем
     * @param string $token
     * @return bool
     */
    public function addCheckToken(string $token) : bool;

    /******************************************************************************************************************
     * РЕГИСТРАЦИЯ
     *****************************************************************************************************************/

    /**
     * регистрация пользователя. данные берутся из формы и создается сущность пользователя
     * @param SignupForm $signupForm
     * @return bool
     */
    public function signUp(SignupForm $signupForm) : bool;

    /**
     * метод вызывается после создания нового пользователя
     * сюда вписывать доп действия - создание связанных сущностей, отсылку писем, уведомлений и прочее
     * @return mixed
     */
    public function afterSignUp();
}