<?php

namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\User\{AbstractUser, UserInterface};
use sorokinmedia\user\handlers\User\UserHandler;
use Yii;
use yii\base\Model;
use yii\db\Exception;
use yii\web\ServerErrorHttpException;

/**
 * Class SignUpFormEmail
 * @package sorokinmedia\user\forms
 *
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $role
 * @property int $status_id
 * @property UserInterface $_user
 */
class SignUpFormEmail extends Model
{
    public $username;
    public $email;
    public $password;
    public $role;
    public $status_id = AbstractUser::STATUS_WAIT_EMAIL;

    private $_user;

    /**
     * SignUpFormEmail constructor.
     * @param array $config
     * @param UserInterface $user
     * @param string $role
     * @param int $status_id
     */
    public function __construct(array $config = [], UserInterface $user, string $role, int $status_id = null)
    {
        parent::__construct($config);
        $this->_user = $user;
        $this->role = $role;
        if ($status_id !== null) {
            $this->status_id = $status_id;
        }
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'username' => Yii::t('app-sm-user', 'Имя пользователя'),
            'email' => Yii::t('app-sm-user', 'E-mail'),
            'password' => Yii::t('app-sm-user', 'Пароль'),
        ];
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => AbstractUser::class, 'message' => Yii::t('app-sm-user', 'Этот E-mail уже зарегистрирован в системе. Попробуйте использовать другой или восстановить пароль, указав текущий.')],
        ];
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \yii\base\Exception
     * @throws ServerErrorHttpException
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

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->_user;
    }

    /**
     * подготовка данных
     * @throws \yii\base\Exception
     */
    public function prepareUsernameAndPassword(): void
    {
        $this->username = str_replace(['@', '.'], '_', $this->email);
        $this->password = Yii::$app->security->generateRandomString(6);
    }
}
