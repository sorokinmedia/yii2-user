<?php
namespace sorokinmedia\user\entities\UserAccessToken;

use sorokinmedia\user\entities\User\AbstractUser;
use yii\db\ActiveQuery;

/**
 * Interface UserAccessTokenInterface
 * @package sorokinmedia\user\entities\UserAccessToken
 */
interface UserAccessTokenInterface
{
    /**
     * получает пользователя по токену
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery;

    /**
     * генерирует токен
     * @param string $string
     * @return string
     */
    public static function generateToken(string $string) : string;

    /**
     * генерирует время жизни токена
     * @param bool $remember
     * @return int
     */
    public static function generateExpired(bool $remember) : int;

    /**
     * статический конструктор
     * @param AbstractUser $user
     * @param bool $remember
     * @return UserAccessTokenInterface
     */
    public static function create(AbstractUser $user, bool $remember = false) : UserAccessTokenInterface;

    /**
     * создание модели в БД
     * @return bool
     */
    public function insertModel() : bool;

    /**
     * деактивирует токен
     * @return bool
     */
    public function deactivate() : bool;
}