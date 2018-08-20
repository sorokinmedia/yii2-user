<?php
namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\User\AbstractUser;
use sorokinmedia\user\handlers\User\UserHandler;
use sorokinmedia\user\entities\User\UserInterface;
use yii\base\Model;
use yii\db\Exception;

/**
 * Class SignupForm
 * @package sorokinmedia\user\forms
 *
 * @property string $username
 * @property string $email
 * @property string $password
 * @property UserInterface $_user
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;

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
     */
    public function __construct(array $config = [], UserInterface $user)
    {
        parent::__construct($config);
        $this->_user = $user;
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
