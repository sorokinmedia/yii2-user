<?php
namespace common\components\user\forms;

use ma3obblu\user\entities\User\User;
use yii\db\Exception;
use yii\base\Model;

/**
 * Class RegisterForm
 * @package common\components\user\forms
 *
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string $repeat
 */
class RegisterForm extends Model
{
    public $email;
    public $username;
    public $password;
    public $repeat;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['email', 'username', 'password', 'repeat'], 'required'],
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'ajaxValidateUsername'],
            ['username', 'match', 'pattern' => '#^[\w_-]+$#i'],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'ajaxValidateEmail'],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
            ['repeat', 'required'],
            ['repeat', 'compare', 'compareAttribute' => 'password', 'message' => \Yii::t('app', 'Введенные пароли не совпадают')],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'email' => \Yii::t('app', 'Email'),
            'password' => \Yii::t('app', 'Пароль'),
            'username' => \Yii::t('app','Имя пользователя'),
            'repeat' => \Yii::t('app','Повторите пароль')
        ];
    }

    /**
     * @return bool
     */
    public function repeat()
    {
        if ($this->password == $this->repeat) {
            return true;
        }
        $this->addError('repeat', 'Пароли не совпадают');
        return false;
    }

    /**
     * @param $attribute
     */
    public function ajaxValidateUsername($attribute)
    {
        $user = User::findOne(['username' => $this->username]);
        if ($user) {
            $this->addError($attribute, 'Пользователь с таким логином уже существует');
        }
    }

    /**
     * @param $attribute
     */
    public function ajaxValidateEmail($attribute)
    {
        $user = User::findOne(['email' => $this->email]);
        if ($user) {
            $this->addError($attribute, 'Пользователь с таким email уже существует');
        }
    }

    /**
     * //TODO: мб лучше несколько сценариев по регистрации разных ролей с разным набором afterRegister действий
     * @return bool
     * @throws Exception
     * @throws \Throwable
     * @throws \yii\base\Exception
     */
    public function register()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->generateEmailConfirmToken();
            $user->activate(); // TODO: решить куда сунуть
            if (!$user->save()) {
                throw new Exception(\Yii::t('app','Не удалось зарегистрировать пользователя. Ошибка #1'));
            }
            $user->save();
            $user->refresh();
            $user->upgradeToRole(User::ROLE_LEARNER); // TODO: default role in component setting
            //TODO: meta,bill,notifications
            //TODO: default mailer from component setting
            \Yii::$app->mailer->compose('@common/mail/emailConfirm', ['user' => $user])
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                ->setTo($user->email)
                ->setSubject('Подтверждение E-mail адреса для ' . \Yii::$app->name)
                ->send();
            \Yii::$app->session->setFlash('success', \Yii::t('app','Вы успешно зарегистрированы'));
            return true;
        }
        return false;
    }
}