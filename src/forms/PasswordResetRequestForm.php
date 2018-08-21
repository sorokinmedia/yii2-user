<?php
namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\User\AbstractUser;
use sorokinmedia\user\entities\User\UserInterface;
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
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'email' => \Yii::t('app', 'E-mail'),
            'password_reset_token_expire' => \Yii::t('app', 'Срок истечения токена'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            [['email', 'reset_password_token_expire'], 'required'],
            ['reset_password_token_expire', 'integer'],
            ['email', 'email'],
        ];
    }

    /**
     * PasswordResetRequestForm constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * сеттер для $_user
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
    {
        $this->_user = $user;
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function sendEmail() : bool
    {
        /** @var AbstractUser $user */
        $user = $this->getUser();
        if (is_null($user)) {
            return false;
        }
        if (!$user->isPasswordResetTokenValid($this->password_reset_token_expire, $user->email_confirm_token)) {
            $user->saveGeneratedPasswordResetToken();
        }
        return $user->sendPasswordResetMail();
    }
}
