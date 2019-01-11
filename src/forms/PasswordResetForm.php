<?php
namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\User\UserInterface;
use yii\base\InvalidArgumentException;
use yii\base\Model;

/**
 * Class PasswordResetForm
 * @package common\components\user\forms
 *
 * @property string $password
 * @property string $password_repeat
 * @property string $token
 * @property UserInterface $_user
 */
class PasswordResetForm extends Model
{
    public $password;
    public $password_repeat;
    public $token;

    private $_user;

    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            [['password', 'password_repeat', 'token'], 'required'],
            [['password', 'password_repeat'], 'string', 'min' => 6],
            [['token'], 'string']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels() : array
    {
        return [
            'password' => \Yii::t('app', 'Пароль'),
            'password_repeat' => \Yii::t('app', 'Повторите пароль'),
        ];
    }

    /**
     * PasswordResetForm constructor.
     * @param array $config
     * @param UserInterface $user
     */
    public function __construct(array $config = [], UserInterface $user)
    {
        parent::__construct($config);
        if (empty($this->token) || !is_string($this->token)) {
            throw new InvalidArgumentException(\Yii::t('app', 'Токен не может быть пустым'));
        }
        $this->_user = $user;
        if (!$this->_user) {
            throw new InvalidArgumentException(\Yii::t('app','Неверный токен. Запросите сброс пароля еще раз'));
        }
    }

    /**
     * @return UserInterface
     */
    public function getUser() : UserInterface
    {
        return $this->_user;
    }

    /**
     * @return bool
     */
    public function checkRepeat() : bool
    {
        if ($this->password === $this->password_repeat) {
            return true;
        }
        $this->addError('password_repeat', \Yii::t('app','Пароли не совпадают'));
        return false;
    }

    /**
     * @return bool
     */
    public function resetPassword() : bool
    {
        $user = $this->getUser();
        if (!$this->checkRepeat()){
            return false;
        }
        $user->removePasswordResetToken();
        return $user->saveNewPassword($this->password, true);
    }
}
