<?php
namespace sorokinmedia\user\entities\User;

use sorokinmedia\user\forms\SignupForm;
use sorokinmedia\user\forms\SignUpFormEmail;
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
     * верифицировать аккаунт - пройдены все проверки
     * @return bool
     */
    public function verifyAccount() : bool;

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

    /**
     * отправка письма с ссылкой на сброс пароля
     * @return mixed
     */
    public function sendPasswordResetMail();

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

    /**
     * отправка письма с ссылкой на подтверждение e-mail'a
     * @return bool
     */
    public function sendEmailConfirmation() : bool;

    /**
     * отправка письма с ссылкой на подтверждение e-mail'a и сгенерированным паролем
     * @param string $password
     * @return bool
     */
    public function sendEmailConfirmationWithPassword(string $password) : bool;

    /******************************************************************************************************************
     * ПОДТВЕРЖДЕНИЕ НОМЕРА ТЕЛЕФОНА
     *****************************************************************************************************************/
    
    
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

    /**
     * получить массив или текстовку ролей
     * @param string|null $role
     * @return mixed
     */
    public static function getRolesArray(string $role = null);

    /**
     * получить массив или ссылку по роли
     * @param string|null $role
     * @return mixed
     */
    public static function getRoleLink(string $role = null);

    /**
     * получение названия основной роли
     * @return string
     */
    public function getPrimaryRole() : string;

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
     * @param string $cookie_url
     * @return bool
     * @deprecated spa
     */
    public function afterLogin(string $cookie_url) : bool;

    /**
     * деактиваирует токен и удаляет куку при логауте
     * @return bool
     * @deprecated spa
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
     * @param string $cookie_url
     * @return bool
     */
    public function addCheckToken(string $token, string $cookie_url) : bool;

    /**
     * обновление даты последнего захода пользователя
     * @return bool
     */
    public function updateLastEntering() : bool;

    /******************************************************************************************************************
     * РЕГИСТРАЦИЯ
     *****************************************************************************************************************/

    /**
     * регистрация пользователя. данные берутся из формы и создается сущность пользователя
     * @param SignupForm $form
     * @return bool
     */
    public function signUp(SignupForm $form) : bool;

    /**
     * регистрация пользователя по email
     * логином будет email с замененнными символами @ и . на _
     * пароль будет сгенерирован и выслан на email
     * @param SignUpFormEmail $form
     * @return bool
     */
    public function signUpEmail(SignUpFormEmail $form) : bool;

    /**
     * метод вызывается после создания нового пользователя
     * сюда вписывать доп действия - назначение роли, создание связанных сущностей, отсылку писем, уведомлений и прочее
     * @param string $role
     * @return mixed
     */
    public function afterSignUp(string $role = null);

    /**
     * метод, вызываемой после создания сущности пользователя по email. требует реализации в дочернем классе.
     * сюда вписывать доп действия - назначение роли, создание связанных сущностей, отсылку писем, уведомлений и прочее
     * @param string $role
     * @return mixed
     */
    public function afterSignUpEmail(string $role = null);

    /******************************************************************************************************************
     * СПИСКИ ПОЛЬЗОВАТЕЛЕЙ
     *****************************************************************************************************************/

    /**
     * массив пользователей в виде id => username
     * @return array
     */
    public static function getUsersArray() : array;

    /**
     * все активные пользователи
     * @return mixed
     */
    public static function getActiveUsers();

    /**
     * отображаемое имя
     * @return string
     */
    public function getDisplayName() : string;

    /******************************************************************************************************************
     * РАБОТА С УВЕДОМЛЕНИЯМИ
     *****************************************************************************************************************/

    /**
     * получение ID телеграма пользователя
     * @param int|null $chat_id
     * @return int|null
     */
    public function getTelegramId(int $chat_id = null);

    /**
     * установка ID телеграма пользователю
     * @param int $id
     * @param string $auth_key
     * @return UserInterface|null
     */
    public static function setTelegramId(int $id, string $auth_key);

    /**
     * включить телеграм в уведомлениях
     * @return bool
     */
    public function telegramOn() : bool;

    /**
     * включить телеграм в уведомлениях
     * @return bool
     */
    public function telegramOff() : bool;
}