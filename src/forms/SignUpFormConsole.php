<?php

namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\User\{AbstractUser, UserInterface};
use sorokinmedia\user\handlers\User\UserHandler;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\web\ServerErrorHttpException;

/**
 * Class SignUpFormConsole
 * @package sorokinmedia\user\forms
 *
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $role
 * @property int $status_id
 * @property array $custom_data
 * @property UserInterface $_user
 */
class SignUpFormConsole extends Model
{
    public $username;
    public $email;
    public $password;
    public $role;
    public $status_id = AbstractUser::STATUS_ACTIVE;
    public $custom_data = [];

    private $_user;

    /**
     * SignUpFormConsole constructor.
     * @param array $config
     * @param UserInterface $user
     * @param string $role
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
            'username' => Yii::t('app', 'Имя пользователя'),
            'email' => Yii::t('app', 'E-mail'),
            'password' => Yii::t('app', 'Пароль'),
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
            ['username', 'unique', 'targetClass' => AbstractUser::class, 'message' => Yii::t('app', 'Этот логин уже занят. Попробуйте использовать другой.')],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => AbstractUser::class, 'message' => Yii::t('app', 'Этот E-mail уже зарегистрирован в системе. Попробуйте использовать другой или восстановить пароль, указав текущий.')],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],

            ['status_id', 'integer']
        ];
    }

    /**
     * @return bool
     * @throws Exception
     * @throws ServerErrorHttpException
     */
    public function signUp(): bool
    {
        $user = $this->getUser();
        if ($this->validate()) {
            (new UserHandler($user))->createFromConsole($this);
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
