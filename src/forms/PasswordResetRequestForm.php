<?php
namespace ma3obblu\user\forms;

use yii\base\Model;
use ma3obblu\user\entities\User\User;

/**
 * Class PasswordResetRequestForm
 * @package common\components\user\forms
 *
 * @property string $email
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    public function attributeLabels()
    {
        return [
            'email' => \Yii::t('app', 'E-mail'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => User::class,
                //'filter' => ['status' => User::STATUS_ACTIVE],
                'message' => \Yii::t('app', 'Пользователь с таким e-mail не найден')
            ],
        ];
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'status' => User::STATUS_ACTIVE,
            'email' => $this->email,
        ]);
        if (is_null($user)) {
            return false;
        }
        if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
        }
        if (!$user->save()) {
            return false;
        }
        return \Yii::$app->mailer
            ->compose('@common/mail/passwordReset',['user' => $user])
            ->setFrom('info@101kurs.com')
            ->setTo($this->email)
            ->setSubject('Сброс пароля 101kurs')
            ->send();
    }
}
