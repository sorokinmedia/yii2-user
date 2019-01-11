<?php

namespace sorokinmedia\user\entities\SmsCode;

use sorokinmedia\user\entities\User\AbstractUser;
use yii\base\{
    Exception, Model
};

/**
 * Class SmsRequestValidator
 * @package sorokinmedia\user\entities\SmsCode
 *
 * @property AbstractUser $user
 * @property string $ip
 * @property int $type_id
 */
class SmsCodeValidator extends Model
{
    public $user;
    public $ip;
    public $type_id;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['user', 'ip'], 'required'],
            ['ip', 'ip']
        ];
    }

    /**
     * валидация на кол-во запросов по ip адресу в день
     * @throws Exception
     */
    public function validateIpSmsCode()
    {
        $codeRequestsCountIp = AbstractSmsCode::getRequestedTodayByIp($this->ip, $this->type_id); // число запросов смс за сегодня
        if ($codeRequestsCountIp >= AbstractSmsCode::MAX_PER_IP) {
            throw $e = new \yii\base\Exception(\Yii::t('app', 'Превышено число попыток с вашего IP адреса. Попробуйте завтра или обратитесь к администратору ресурса'));
        }
    }

    /**
     * валидация на кол-во запросов на юзера в день
     * @throws Exception
     */
    public function validatePhoneSmsCode()
    {
        $codeRequestsCountPhone = AbstractSmsCode::getRequestedTodayByUser($this->user, $this->type_id); // число запросов смс за сегодня
        if ($codeRequestsCountPhone >= AbstractSmsCode::MAX_PER_DAY) {
            throw $e = new \yii\base\Exception(\Yii::t('app', 'Превышено число попыток. Попробуйте завтра или обратитесь к администратору ресурса'));
        }
    }
}