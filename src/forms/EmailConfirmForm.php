<?php
namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\User\UserInterface;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * Class EmailConfirmForm
 * @package common\components\user\forms
 *
 * @property UserInterface $_user
 */
class EmailConfirmForm extends Model
{
    private $_user;

    /**
     * EmailConfirmForm constructor.
     * @param string $token
     * @param array $config
     */
    public function __construct(array $config = [], string $token, UserInterface $user)
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException(\Yii::t('app','Отсутствует код подтверждения.'));
        }
        $this->_user = $user;
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
        return $user->confirmEmailAction();
    }
}