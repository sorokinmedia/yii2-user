<?php

namespace sorokinmedia\user\forms;

use sorokinmedia\helpers\DateHelper;
use sorokinmedia\user\entities\{User\AbstractUser, User\UserInterface};
use yii\base\{InvalidArgumentException, Model};
use Yii;

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
     * LoginForm constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],
            [['email'], 'string'],
            ['remember', 'boolean'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'email' => Yii::t('app', 'Email'),
            'password' => Yii::t('app', 'Пароль'),
            'remember' => Yii::t('app', 'Запомнить меня'),
        ];
    }

    /**
     * @param $attribute
     * @param $params
     */
    public function validatePassword($attribute, $params): void
    {
        if (!$this->hasErrors()) {
            /** @var AbstractUser $user */
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError('password', Yii::t('app', 'Логин или пароль указан не верно. Попробуйте еще раз.'));
            }
        }
    }

    /**
     * геттер
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->_user;
    }

    /**
     * сеттер
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user): void
    {
        $this->_user = $user;
    }

    /**
     * валидация статусов для API
     * @return bool
     */
    public function validateStatusApi(): bool
    {
        /** @var AbstractUser $user */
        $user = $this->getUser();
        if ($user && $user->status_id === AbstractUser::STATUS_BLOCKED) {
            throw new InvalidArgumentException(Yii::t('app', 'Ваш аккаунт заблокирован. Обратитесь к тех.поддержке.'));
        }
        if ($user && $user->status_id === AbstractUser::STATUS_WAIT_EMAIL) {
            throw new InvalidArgumentException(Yii::t('app', 'Ваш аккаунт не подтвержден. Необходимо подтвердить e-mail.'));
        }
        return true;
    }

    /**
     * логин пользователя
     * @return bool
     */
    public function login(): bool
    {
        if ($this->validate()) {
            $this->validateStatus();
            if (!$this->hasErrors()) {
                return Yii::$app->user->login($this->_user, $this->remember ? DateHelper::TIME_DAY_THIRTY : 0);
            }
        }
        return false;
    }

    /**
     * валидация статуса
     */
    public function validateStatus(): void
    {
        /** @var AbstractUser $user */
        $user = $this->getUser();
        if ($user && $user->status_id === AbstractUser::STATUS_BLOCKED) {
            $this->addError('login', Yii::t('app', 'Ваш аккаунт заблокирован. Обратитесь к тех.поддержке.'));
        } elseif ($user && $user->status_id === AbstractUser::STATUS_WAIT_EMAIL) {
            $this->addError('login', Yii::t('app', 'Ваш аккаунт не подтвержден. Необходимо подтвердить e-mail.'));
        }
    }
}
