<?php

namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\User\UserInterface;
use yii\base\{InvalidArgumentException, Model};
use Yii;

/**
 * Class EmailConfirmForm
 * @package common\components\user\forms
 *
 * @property string $token
 * @property UserInterface $_user
 */
class EmailConfirmForm extends Model
{
    public $token;
    private $_user;

    /**
     * EmailConfirmForm constructor.
     * @param array $config
     * @param UserInterface $user
     */
    public function __construct(array $config = [], UserInterface $user)
    {
        parent::__construct($config);
        if (empty($this->token) || !is_string($this->token)) {
            throw new InvalidArgumentException(Yii::t('app-sm-user', 'Отсутствует код подтверждения.'));
        }
        $this->_user = $user;
        if (!$this->_user) {
            throw new InvalidArgumentException(Yii::t('app-sm-user', 'Неверный токен.'));
        }
    }

    /**
     * подтвердить email
     * @return bool
     */
    public function confirmEmail(): bool
    {
        $user = $this->_user;
        return $user->confirmEmailAction();
    }
}
