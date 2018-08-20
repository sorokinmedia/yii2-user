<?php
namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\User\UserInterface;
use yii\base\Model;

/**
 * Class PasswordResetForm
 * @package common\components\user\forms
 *
 * @property string $password
 * @property string $password_repeat
 *
 * @property UserInterface $_user
 */
class PasswordChangeForm extends Model
{
    public $password;
    public $password_repeat;

    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['password', 'password_repeat'], 'required'],
            [['password', 'password_repeat'], 'string', 'min' => 6],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'password' => \Yii::t('app', 'Пароль'),
            'password_repeat' => \Yii::t('app', 'Повторите пароль'),
        ];
    }

    /**
     * PasswordChangeForm constructor.
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
     * @throws \yii\base\Exception
     */
    public function changePassword() : bool
    {
        $user = $this->_user;
        if (!$this->checkRepeat()){
            return false;
        }
        return $user->saveNewPassword($this->password);
    }
}
