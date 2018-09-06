<?php
namespace sorokinmedia\user\tests\entities\SmsCode;

use sorokinmedia\user\entities\SmsCode\AbstractSmsCode;
use sorokinmedia\user\tests\entities\User\RelationClassTrait;

/**
 * Class SmsCode
 * @package sorokinmedia\user\tests\entities\SmsCode
 */
class SmsCode extends AbstractSmsCode
{
    use RelationClassTrait;

    const TYPE_VERIFY = 1;

    /**
     * @return bool
     */
    public function sendCode() : bool
    {
        return true;
    }

    /**
     * @param int|null $type_id
     * @return array|mixed
     */
    public static function getTypes(int $type_id = null)
    {
        $types = [
            self::TYPE_VERIFY => \Yii::t('app', 'Верификация'),
        ];
        if (!is_null($type_id)){
            return $types[$type_id];
        }
        return $types;
    }

    /**
     * @return string
     */
    public function getMessage() : string
    {
        switch ($this->type_id){
            case self::TYPE_VERIFY:
                return \Yii::t('app', 'Код проверки {code}', [
                    'code' => $this->code
                ]);
            default:
                return '';
        }
    }

    /**
     * @return int
     */
    public function generateCode() : int
    {
        return rand(1000, 9999);
    }
}