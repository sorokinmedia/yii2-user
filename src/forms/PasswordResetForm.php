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
 *
 * @property UserInterface $_user
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
     * @param UserInterface $user
     * @param string $token
     * @param array $config
     */
    public function __construct(array $config = [],string $token, UserInterface $user)
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidArgumentException(\Yii::t('app', 'Токен не может быть пустым'));
        }
        $this->_user = $user;
        if (!$this->_user) {
            throw new InvalidArgumentException(\Yii::t('app','Неверный токен'));
        }
        parent::__construct($config);
    }

    /**
     * @return bool
     */
    public function resetPassword() : bool
    {
        $user = $this->_user;
        return $user->saveNewPassword($this->password, true);
    }
}
