<?php
namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\User\UserInterface;
use yii\base\Model;

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
        ];
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     * //TODO: mailer from component settings
     */
    public function sendEmail(UserInterface $user)
    {
        if (is_null($user)) {
            return false;
        }
        if (!$user->isPasswordResetTokenValid()) {
            $user->saveGeneratedPasswordResetToken();
        }
        return \Yii::$app->mailer
            ->compose('@common/mail/passwordReset',['user' => $user])
            ->setFrom('info@101kurs.com')
            ->setTo($this->email)
            ->setSubject('Сброс пароля 101kurs')
            ->send();
    }
}
