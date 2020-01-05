<?php

namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\User\UserInterface;
use Yii;
use yii\base\Model;

/**
 * Class PasswordResetForm
 * @package common\components\user\forms
 *
 * @property string $password
 * @property string $password_repeat
 *
 * @property UserInterface $_user
 */
class PasswordChangeForm extends Model
{
    public $password;
    public $password_repeat;

    private $_user;

    /**
     * PasswordChangeForm constructor.
     * @param array $config
     * @param UserInterface $user
     */
    public function __construct(array $config = [], UserInterface $user)
    {
        parent::__construct($config);
        $this->_user = $user;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['password', 'password_repeat'], 'required'],
            [['password'], 'string', 'min' => 6],
            [['password_repeat'], 'string'],
            [['password_repeat'], 'compare', 'compareAttribute' => 'password', 'message' => Yii::t('sm-user', 'Пароли не совпадают')]
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'password' => Yii::t('sm-user', 'Пароль'),
            'password_repeat' => Yii::t('sm-user', 'Повторите пароль'),
        ];
    }

    /**
     * @return bool
     */
    public function changePassword(): bool
    {
        $user = $this->getUser();
        if (!$this->checkRepeat()) {
            return false;
        }
        return $user->saveNewPassword($this->password);
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->_user;
    }

    /**
     * @return bool
     */
    public function checkRepeat(): bool
    {
        if ($this->password === $this->password_repeat) {
            return true;
        }
        $this->addError('password_repeat', Yii::t('sm-user', 'Пароли не совпадают'));
        return false;
    }
}
