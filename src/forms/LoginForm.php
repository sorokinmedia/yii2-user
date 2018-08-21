<?php
namespace sorokinmedia\user\forms;

use sorokinmedia\helpers\DateHelper;
use sorokinmedia\user\entities\User\AbstractUser;
use sorokinmedia\user\entities\User\UserInterface;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * Class LoginForm
 * @package sorokinmedia\user\forms
 *
 * @property string $email
 * @property string $password
 * @property bool $remember
 *
 * @property AbstractUser $_user
 */
class LoginForm extends Model
{
    public $email;
    public $password;
    public $remember = true;

    private $_user;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            [['email'], 'email'],
            ['remember', 'boolean'],
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
            'remember' => \Yii::t('app','Запомнить меня'),
        ];
    }

    /**
     * LoginForm constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * сеттер
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->_user = $user;
    }

    /**
     * геттер
     * @return UserInterface
     */
    public function getUser() : UserInterface
    {
        return $this->_user;
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            /** @var AbstractUser $user */
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', \Yii::t('app', 'Логин или пароль указан не верно. Попробуйте еще раз.'));
            }
        }
    }

    /**
     * валидация статуса
     */
    public function validateStatus()
    {
        /** @var AbstractUser $user */
        $user = $this->getUser();
        if ($user && $user->status_id == AbstractUser::STATUS_BLOCKED) {
            $this->addError('login', \Yii::t('app', 'Ваш аккаунт заблокирован. Обратитесь к тех.поддержке.'));
        } elseif ($user && $user->status_id == AbstractUser::STATUS_WAIT) {
            $this->addError('login', \Yii::t('app', 'Ваш аккаунт не подтвержден. Необходимо подтвердить e-mail.'));
        }
    }

    /**
     * валидация статусов для API
     * @return bool
     */
    public function validateStatusApi() : bool
    {
        /** @var AbstractUser $user */
        $user = $this->getUser();
        if ($user && $user->status_id == AbstractUser::STATUS_BLOCKED) {
            throw new InvalidArgumentException(\Yii::t('app', 'Ваш аккаунт заблокирован. Обратитесь к тех.поддержке.'));
        } elseif ($user && $user->status_id == AbstractUser::STATUS_WAIT) {
            throw new InvalidArgumentException(\Yii::t('app', 'Ваш аккаунт не подтвержден. Необходимо подтвердить e-mail.'));
        }
        return true;
    }

    /**
     * логин пользователя
     * @return bool
     */
    public function login() : bool
    {
        if ($this->validate()) {
            $this->validateStatus();
            if (!$this->hasErrors()){
                return \Yii::$app->user->login($this->_user, $this->remember ? DateHelper::TIME_DAY_THIRTY : 0);
            }
        }
        return false;
    }
}
