<?php
namespace ma3obblu\user\forms;

use ma3obblu\user\entities\User\User;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * Class EmailConfirmForm
 * @package common\components\user\forms
 *
 * @property User $_user
 */
class EmailConfirmForm extends Model
{
    private $_user;

    /**
     * EmailConfirmForm constructor.
     * @param string $token
     * @param array $config
     */
    public function __construct(string $token, array $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException(\Yii::t('app','Отсутствует код подтверждения.'));
        }
        $this->_user = User::findByEmailConfirmToken($token);
        if (!$this->_user) {
            throw new InvalidArgumentException(\Yii::t('app','Неверный токен.'));
        }
        parent::__construct($config);
    }

    /**
     * подтвердить email
     * @return bool
     */
    public function confirmEmail() : bool
    {
        $user = $this->_user;
        $user->status_id = User::STATUS_ACTIVE;
        $user->removeEmailConfirmToken();
        return $user->save();
    }
}