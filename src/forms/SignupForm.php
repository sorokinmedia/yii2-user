<?php
namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\User\AbstractUser;
use sorokinmedia\user\handlers\User\UserHandler;
use sorokinmedia\user\entities\User\UserInterface;
use yii\base\Model;

/**
 * Class SignupForm
 * @package sorokinmedia\user\forms
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => \Yii::t('app', 'Username'),
            'email' => \Yii::t('app', 'E-mail'),
            'password' => \Yii::t('app', 'Password'),
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
     * @param UserInterface $user
     * @return bool
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function signUp(UserInterface $user) : bool
    {
        if ($this->validate()) {
            (new UserHandler($user))->create($this);
            return true;
        }
        throw new \InvalidArgumentException(\Yii::t('app','Ошибка валидации данных при регистрации'));
    }
}
