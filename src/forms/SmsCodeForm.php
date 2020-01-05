<?php

namespace sorokinmedia\user\forms;

use sorokinmedia\user\entities\SmsCode\AbstractSmsCode;
use Yii;
use yii\base\Model;

/**
 * Class SmsCodeForm
 * @package sorokinmedia\user\forms
 *
 * @property int $user_id
 * @property string $phone
 * @property int $code
 * @property int $type_id
 * @property string $ip
 * @property int $is_used
 * @property bool $is_validated
 * @property bool $is_deleted
 */
class SmsCodeForm extends Model
{
    public $user_id;
    public $phone;
    public $code;
    public $type_id;
    public $ip;
    public $is_used;
    public $is_validated;
    public $is_deleted;

    /**
     * SmsCodeForm constructor.
     * @param array $config
     * @param AbstractSmsCode|null $smsCode
     */
    public function __construct(array $config = [], AbstractSmsCode $smsCode = null)
    {
        if ($smsCode !== null) {
            $this->user_id = $smsCode->user_id;
            $this->phone = $smsCode->phone;
            $this->code = $smsCode->code;
            $this->type_id = $smsCode->type_id;
            $this->ip = $smsCode->ip;
            $this->is_used = $smsCode->is_used;
            $this->is_validated = $smsCode->is_validated;
            $this->is_deleted = $smsCode->is_deleted;
        }
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['user_id', 'type_id', 'is_used', 'code'], 'integer'],
            [['ip'], 'ip'],
            [['phone'], 'string', 'max' => 12],
            [['is_validated', 'is_deleted'], 'boolean'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'user_id' => Yii::t('sm-user', 'Пользователь'),
            'phone' => Yii::t('sm-user', 'Номер телефона'),
            'code' => Yii::t('sm-user', 'Код'),
            'type_id' => Yii::t('sm-user', 'Тип'),
            'ip' => Yii::t('sm-user', 'IP'),
            'is_used' => Yii::t('sm-user', 'Кол-во использований'),
            'is_validated' => Yii::t('sm-user', 'Проверен'),
            'is_deleted' => Yii::t('sm-user', 'Удален'),
        ];
    }
}
