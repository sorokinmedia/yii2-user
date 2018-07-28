<?php
namespace sorokinmedia\user\forms;

use yii\base\Model;
use sorokinmedia\user\entities\User\UserInterface;
use yii\web\IdentityInterface;

/**
 * Class LoginForm
 * @package sorokinmedia\user\forms
 *
 * @property string $email
 * @property string $password
 * @property bool $rememberMe
 *
 * @property UserInterface $_user
 */
class LoginForm extends Model
{
    const THIRTY_DAYS = 3600*24*30;

    public $email;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            [['email'], 'email'],
            ['rememberMe', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'email' => \Yii::t('app', 'Email'),
            'password' => \Yii::t('app','Пароль'),
            'rememberMe' => \Yii::t('app','Запомнить меня'),
        ];
    }

    /**
     * валидация пароля
     * @param string $attribute
     */
    public function validatePassword(string $attribute)
    {
        if (!$this->hasErrors()) {
            $user = $this->_getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, \Yii::t('app','Неверно указан логин или пароль'));
            }
        }
    }

    /**
     * @return UserInterface
     */
    private function _getUser()
    {
        if ($this->_user === false) {
            $this->_user = UserInterface::findByEmail($this->email);
        }
        return $this->_user;
    }

    /**
     * логин пользователя
     * @param IdentityInterface $user
     * @return bool
     */
    public function login(IdentityInterface $user) : bool
    {
        if ($this->validate()) {
            return \Yii::$app->user->login($user, $this->rememberMe ? self::THIRTY_DAYS : 0);
        }
        return false;
    }
}
