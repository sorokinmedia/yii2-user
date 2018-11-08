<?php
namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\User\{
    AbstractUser,UserInterface
};
use sorokinmedia\user\handlers\User\UserHandler;
use yii\base\Model;
use yii\db\Exception;

/**
 * Class SignupForm
 * @package sorokinmedia\user\forms
 *
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $role
 * @property UserInterface $_user
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $role;

    private $_user;

    /**
     * @return array
     */
    public function attributeLabels()
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
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'match', 'pattern' => '#^[\w_-]+$#i'],
            ['username', 'unique', 'targetClass' => AbstractUser::class, 'message' => \Yii::t('app', 'Этот логин уже занят. Попробуйте использовать другой.')],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => AbstractUser::class, 'message' => \Yii::t('app', 'Этот E-mail уже зарегистрирован в системе. Попробуйте использовать другой или восстановить пароль, указав текущий.')],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * SignupForm constructor.
     * @param array $config
     * @param UserInterface $user
     * @param string $role
     */
    public function __construct(array $config = [], UserInterface $user, string $role = null)
    {
        parent::__construct($config);
        $this->_user = $user;
        if (!is_null($role)){
            $this->role = $role;
        }
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function signUp() : bool
    {
        $user = $this->getUser();
        if ($this->validate()) {
            (new UserHandler($user))->create($this);
            return true;
        }
        return false;
    }
}
