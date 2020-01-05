<?php

namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\User\{AbstractUser, UserInterface};
use sorokinmedia\user\handlers\User\UserHandler;
use Yii;
use yii\base\Model;
use yii\web\ServerErrorHttpException;

/**
 * Class SignUpFormExisted
 * @package sorokinmedia\user\forms
 *
 * форма для переноса пользователей между проектами
 * в password приходит уже сгенерированный хеш пароля, используем его
 * не учитываем аффилиатов и инвайты
 *
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $role
 * @property UserInterface $_user
 * @property bool $old_user
 */
class SignUpFormExisted extends Model
{
    public string $username;
    public string $email;
    public string $password;
    public string $role;

    private UserInterface $_user;

    /**
     * SignUpFormExisted constructor.
     * @param array $config
     * @param UserInterface $user
     * @param string|null $role
     */
    public function __construct(array $config = [], UserInterface $user, string $role = null)
    {
        parent::__construct($config);
        $this->_user = $user;
        if ($role !== null) {
            $this->role = $role;
        }
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'username' => Yii::t('sm-user', 'Имя пользователя'),
            'email' => Yii::t('sm-user', 'E-mail'),
            'password' => Yii::t('sm-user', 'Пароль'),
        ];
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'match', 'pattern' => '#^[\w_-]+$#i'],
            ['username', 'unique', 'targetClass' => AbstractUser::class, 'message' => Yii::t('sm-user', 'Этот логин уже занят. Попробуйте использовать другой.')],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => AbstractUser::class, 'message' => Yii::t('sm-user', 'Этот E-mail уже зарегистрирован в системе. Попробуйте использовать другой или восстановить пароль, указав текущий.')],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * @return bool
     * @throws ServerErrorHttpException
     */
    public function signUp(): bool
    {
        $user = $this->getUser();
        if ($this->validate()) {
            (new UserHandler($user))->createExisted($this);
            return true;
        }
        return false;
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->_user;
    }
}
