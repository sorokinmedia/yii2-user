<?php

namespace sorokinmedia\user\entities\User;

use sorokinmedia\user\forms\{SignupForm, SignUpFormEmail};
use yii\db\ActiveQuery;
use yii\rbac\Role;

/**
 * Interface UserInterface
 * @package sorokinmedia\user\entities\User
 */
interface UserInterface
{
    /**
     * список статусов
     * @return array
     */
    public static function getStatusesArray(): array;

    /**
     * получает объект роли по ее названию
     * @param string $role_name
     * @return null|Role
     */
    public static function getRole(string $role_name): ?Role;

    /**
     * поиск пользователя по токену сброса пароля
     * @param int $expired
     * @param string $token
     * @return AbstractUser
     */
    public static function findByPasswordResetToken(int $expired, string $token = null): AbstractUser;

    /**
     * проверка валидности токена сброса пароля
     * @param int $expired
     * @param string $token
     * @return bool
     */
    public static function isPasswordResetTokenValid(int $expired, string $token = null): bool;

    /**
     * поиск пользователя по токену подтверждения e-mail
     * @param string $email_confirm_token
     * @return null|AbstractUser
     */
    public static function findByEmailConfirmToken(string $email_confirm_token): ?AbstractUser;

    /**
     * поиск пользователя по e-mail
     * @param string $email
     * @return null|UserInterface
     */
    public static function findByEmail(string $email): ?UserInterface;

    /**
     * поиск пользователей по роли
     * @param string $role
     * @return array
     */
    public static function findByRole(string $role): array;

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
     * массив пользователей в виде id => username
     * @return array
     */
    public static function getUsersArray(): array;

    /**
     * все активные пользователи
     * @return array
     */
    public static function getActiveUsers(): array;

    /**
     * установка ID телеграма пользователю
     * @param int $id
     * @param string $auth_key
     * @return AbstractUser|null
     */
    public static function setTelegramId(int $id, string $auth_key): ?AbstractUser;

    /**
     * текстовое обозначение статуса
     * @return string
     */
    public function getStatus(): string;

    /**
     * смена статуса на активный
     * @return void
     */
    public function activate(): void;

    /**
     * смена статуса на заблокированный
     * @return void
     */
    public function deactivate(): void;

    /**
     * блокировака юзера
     * @return bool
     */
    public function blockUser(): bool;

    /**
     * действия, которые необходимо сделать после блокировки
     * @return bool
     */
    public function afterBlockUser(): bool;

    /**
     * разблокировка юзера
     * @return bool
     */
    public function unblockUser(): bool;

    /**
     * действия, которые необходимо сделать после разблокировки
     * @return bool
     */
    public function afterUnblockUser(): bool;

    /**
     * верифицировать аккаунт - пройдены все проверки
     * @return bool
     */
    public function verifyAccount(): bool;

    /**
     * генерация токена сброса пароля
     * @return void
     */
    public function generatePasswordResetToken(): void;

    /**
     * сохранить токен для сброса пароля
     * @return bool
     */
    public function saveGeneratedPasswordResetToken(): bool;

    /**
     * удаление токена сброса пароля
     * @return void
     */
    public function removePasswordResetToken(): void;

    /**
     * отправка письма с ссылкой на сброс пароля
     * @return bool
     */
    public function sendPasswordResetMail(): bool;

    /**
     * генерация токена подтверждения e-mail
     * @return void
     */
    public function generateEmailConfirmToken(): void;

    /**
     * удаление токена подтверждения e-mail
     * @return void
     */
    public function removeEmailConfirmToken(): void;

    /**
     * действия при подтверждении e-mail
     * @return bool
     */
    public function confirmEmailAction(): bool;

    /**
     * отправка письма с ссылкой на подтверждение e-mail'a
     * @return bool
     */
    public function sendEmailConfirmation(): bool;

    /**
     * отправка письма с ссылкой на подтверждение e-mail'a и сгенерированным паролем
     * @param string $password
     * @return bool
     */
    public function sendEmailConfirmationWithPassword(string $password): bool;

    /**
     * валидация пароля
     * @param string $password
     * @return bool
     */
    public function validatePassword(string $password): bool;

    /**
     * сохранение пароля
     * @param string $password
     * @return void
     */
    public function setPassword(string $password): void;

    /**
     * сохранение нового пароля
     * @param string $password
     * @param bool $reset_token
     * @return bool
     */
    public function saveNewPassword(string $password, bool $reset_token = false): bool;

    /**
     * генерация API ключа
     * @return void
     */
    public function generateAuthKey(): void;

    /**
     * добавить указанную роль
     * @param Role $role
     * @return bool
     */
    public function upgradeToRole(Role $role): bool;

    /**
     * удалить указанную роль
     * @param Role $role
     * @return bool
     */
    public function downgradeFromRole(Role $role): bool;

    /**
     * получение названия основной роли
     * @return string
     */
    public function getPrimaryRole(): string;

    /**
     * получить все токены пользователя
     * @return ActiveQuery
     */
    public function getTokens(): ActiveQuery;

    /**
     * получает токен из кук
     * @return string
     */
    public function getToken(): string;

    /**
     * деактивировать все токены пользователя
     * @return bool
     */
    public function deactivateTokens(): bool;

    /**
     * проставляет куку и токен после логина
     * @param string $cookie_url
     * @return bool
     * @deprecated spa
     */
    public function afterLogin(string $cookie_url): bool;

    /**
     * деактиваирует токен и удаляет куку при логауте
     * @return bool
     * @deprecated spa
     */
    public function afterLogout(): bool;

    /**
     * получает токен пользователя, под которым нужно войти
     * @return string
     */
    public function getCheckToken(): string;

    /**
     * заменяет токен при заходе под другим пользователем
     * @param string $token
     * @param string $cookie_url
     * @return bool
     */
    public function addCheckToken(string $token, string $cookie_url): bool;

    /**
     * обновление даты последнего захода пользователя
     * @return bool
     */
    public function updateLastEntering(): bool;

    /**
     * регистрация пользователя. данные берутся из формы и создается сущность пользователя
     * @param SignupForm $form
     * @return bool
     */
    public function signUp(SignupForm $form): bool;

    /**
     * регистрация пользователя по email
     * логином будет email с замененнными символами @ и . на _
     * пароль будет сгенерирован и выслан на email
     * @param SignUpFormEmail $form
     * @return bool
     */
    public function signUpEmail(SignUpFormEmail $form): bool;

    /**
     * метод вызывается после создания нового пользователя
     * сюда вписывать доп действия - назначение роли, создание связанных сущностей, отсылку писем, уведомлений и прочее
     * @param string $role
     * @return mixed
     */
    public function afterSignUp(string $role = null);

    /**
     * метод, вызываемый после создания сущности пользователя по email. требует реализации в дочернем классе.
     * сюда вписывать доп действия - назначение роли, создание связанных сущностей, отсылку писем, уведомлений и прочее
     * @param string $role
     * @return mixed
     */
    public function afterSignUpEmail(string $role = null);

    /**
     * метод, вызываемый после создания сущности пользователя консольным способом (регистрации по апи и т.д.)
     * сюда вписывать доп действия - назначение роли, создание связанных сущностей, отсылку писем, уведомлений и прочее
     * @param string|null $role
     * @param array $custom_data
     * @return mixed
     */
    public function afterSignUpConsole(string $role = null, array $custom_data = []);

    /**
     * отображаемое имя
     * @return string
     */
    public function getDisplayName(): string;

    /**
     * получение ID телеграма пользователя
     * @return int|null
     */
    public function getTelegramId(): ?int;

    /**
     * включить телеграм в уведомлениях
     * @return bool
     */
    public function telegramOn(): bool;

    /**
     * включить телеграм в уведомлениях
     * @return bool
     */
    public function telegramOff(): bool;

    /**
     * собрать номер телефона
     * @return string
     */
    public function getPhone(): string;

    /**
     * получить e-mail, на который отправлять уведомления
     * @return string
     */
    public function getNotificationEmail(): string;
}
