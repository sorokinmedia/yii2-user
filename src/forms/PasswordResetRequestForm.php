<?php

namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\{User\AbstractUser, User\UserInterface};
use Yii;
use yii\base\Exception;
use yii\base\Model;

/**
 * Class PasswordResetRequestForm
 * @package common\components\user\forms
 *
 * @property string $email
 * @property int $password_reset_token_expire
 * @property UserInterface $_user
 */
class PasswordResetRequestForm extends Model
{
    public $email;
    public $password_reset_token_expire;
    private $_user;

    /**
     * PasswordResetRequestForm constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'email' => Yii::t('app', 'E-mail'),
            'password_reset_token_expire' => Yii::t('app', 'Срок истечения токена'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            [['email', 'reset_password_token_expire'], 'required'],
            ['reset_password_token_expire', 'integer'],
            ['email', 'email'],
        ];
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function sendEmail(): bool
    {
        /** @var AbstractUser $user */
        $user = $this->getUser();
        if ($user === null) {
            return false;
        }
        if (!AbstractUser::isPasswordResetTokenValid($this->password_reset_token_expire, $user->email_confirm_token)) {
            $user->saveGeneratedPasswordResetToken();
        }
        return $user->sendPasswordResetMail();
    }

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface
    {
        return $this->_user;
    }

    /**
     * сеттер для $_user
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user): void
    {
        $this->_user = $user;
    }
}
