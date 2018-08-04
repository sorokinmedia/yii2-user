<?php
namespace sorokinmedia\user\forms;

use sorokinmedia\helpers\DateHelper;
use sorokinmedia\user\entities\User\AbstractUser;
use yii\base\Model;
use yii\web\IdentityInterface;

/**
 * Class LoginForm
 * @package sorokinmedia\user\forms
 *
 * @property string $email
 * @property string $password
 * @property bool $rememberMe
 *
 * @property AbstractUser $_user
 */
class LoginForm extends Model
{
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
     * @param $attribute
     * @param $params
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->_getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', \Yii::t('app', 'Логин или пароль указан не верно. Попробуйте еще раз.'));
            } elseif ($user && $user->status_id == AbstractUser::STATUS_BLOCKED) {
                $this->addError('login', \Yii::t('app', 'Ваш аккаунт заблокирован. Обратитесь к тех.поддержке.'));
            } elseif ($user && $user->status_id == AbstractUser::STATUS_WAIT) {
                $this->addError('login', \Yii::t('app', 'Ваш аккаунт не подтвержден. Необходимо подтвердить e-mail.'));
            }
        }
    }

    /**
     * @return AbstractUser
     */
    private function _getUser()
    {
        if ($this->_user === false) {
            $this->_user = AbstractUser::findByEmail($this->email);
        }
        return $this->_user;
    }

    /**
     * логин пользователя
     * @return bool
     */
    public function login() : bool
    {
        if ($this->validate()) {
            return \Yii::$app->user->login($this->_getUser(), $this->rememberMe ? DateHelper::TIME_DAY_THIRTY : 0);
        }
        return false;
    }
}
