<?php

namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\User\{
    AbstractUser, UserInterface
};
use sorokinmedia\user\handlers\User\UserHandler;
use yii\base\Model;
use yii\db\Exception;

/**
 * Class SignUpFormEmail
 * @package sorokinmedia\user\forms
 *
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $role
 * @property UserInterface $_user
 */
class SignUpFormEmail extends Model
{
    public $username;
    public $email;
    public $password;
    public $role;

    private $_user;

    /**
     * @return array
     */
    public function attributeLabels() : array
    {
        return [
            'username' => \Yii::t('app', 'Имя пользователя'),
            'email' => \Yii::t('app', 'E-mail'),
            'password' => \Yii::t('app', 'Пароль'),
        ];
    }

    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => AbstractUser::class, 'message' => \Yii::t('app', 'Этот E-mail уже зарегистрирован в системе. Попробуйте использовать другой или восстановить пароль, указав текущий.')],
        ];
    }

    /**
     * SignUpFormEmail constructor.
     * @param array $config
     * @param UserInterface $user
     * @param string $role
     */
    public function __construct(array $config = [], UserInterface $user, string $role)
    {
        parent::__construct($config);
        $this->_user = $user;
        $this->role = $role;
    }

    /**
     * @return UserInterface
     */
    public function getUser() : UserInterface
    {
        return $this->_user;
    }

    /**
     * подготовка данных
     * @throws \yii\base\Exception
     */
    public function prepareUsernameAndPassword()
    {
        $this->username = str_replace(['@', '.'], '_', $this->email);
        $this->password = \Yii::$app->security->generateRandomString(6);
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \yii\base\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function signUp(): bool
    {
        $user = $this->getUser();
        $this->prepareUsernameAndPassword();
        if ($this->validate()) {
            (new UserHandler($user))->createFromEmail($this);
            return true;
        }
        return false;
    }
}
