<?php
namespace sorokinmedia\user\entities\UserAccessToken;

use sorokinmedia\user\entities\User\AbstractUser;
use yii\db\Exception;

/**
 * Class UserAccessToken
 * @package sorokinmedia\user\entities\UserAccessToken
 */
class UserAccessToken extends AbstractUserAccessToken
{
    /**
     * статический конструктор
     * @param AbstractUser $user
     * @param bool $remember
     * @return UserAccessTokenInterface
     * @throws Exception
     */
    public static function create(AbstractUser $user, bool $remember = false) : UserAccessTokenInterface
    {
        $token = self::find()->where(['user_id' => $user->id, 'is_active' => 1])->orderBy(['created_at' => SORT_DESC])->one();
        if ($token instanceof AbstractUserAccessToken && $token->expired_at > time()){
            return $token;
        }
        $user->deactivateTokens();
        $new_token = new static([
            'user_id' => $user->id,
            'access_token' => self::generateToken($user->email),
            'expired_at' => self::generateExpired($remember),
            'is_active' => 1,
        ]);
        if (!$new_token->save()){
            throw new Exception(\Yii::t('app', 'Ошибка при добавлении токена'));
        }
        return $new_token;
    }
}