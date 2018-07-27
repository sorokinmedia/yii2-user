<?php
namespace ma3obblu\user\forms;

use yii\base\Model;
use ma3obblu\user\entities\User\User;

/**
 * Class PasswordResetForm
 * @package common\components\user\forms
 *
 * @property string $password
 * @property string $password_repeat
 *
 * @property User $_user
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
     * @return bool
     */
    public function checkRepeat() : bool
    {
        if ($this->password === $this->password_repeat) {
            return true;
        }
        $this->addError('repeat', \Yii::t('app','Пароли не совпадают'));
        return false;
    }

    /**
     * PasswordChangeForm constructor.
     * @param array $config
     * @param User $user
     */
    public function __construct(array $config = [], User $user)
    {
        $this->_user = $user;
        parent::__construct($config);
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
        $user->setPassword($this->password);
        return $user->save();
    }
}
