<?php
namespace sorokinmedia\user\forms;

use yii\base\InvalidArgumentException;
use yii\base\Model;
use sorokinmedia\user\entities\User\User;

/**
 * Class PasswordResetForm
 * @package common\components\user\forms
 *
 * @property string $password
 *
 * @property User $_user
 */
class PasswordResetForm extends Model
{
    public $password;

    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'password' => \Yii::t('app', 'Пароль'),
        ];
    }

    /**
     * PasswordResetForm constructor.
     * @param string $token
     * @param array $config
     */
    public function __construct(string $token, array $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException(\Yii::t('app', 'Токен не может быть пустым'));
        }
        $this->_user = User::findByPasswordResetToken($token);
        if (!$this->_user) {
            throw new InvalidArgumentException(\Yii::t('app','Неверный токен'));
        }
        parent::__construct($config);
    }

    /**
     * @return bool
     * @throws \yii\base\Exception
     */
    public function resetPassword() : bool
    {
        $user = $this->_user;
        $user->setPassword($this->password);
        $user->removePasswordResetToken();
        return $user->save();
    }
}
