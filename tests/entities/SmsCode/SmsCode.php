<?php

namespace sorokinmedia\user\tests\entities\SmsCode;

use Exception;
use sorokinmedia\user\entities\SmsCode\AbstractSmsCode;
use sorokinmedia\user\tests\entities\User\RelationClassTrait;
use Yii;

/**
 * Class SmsCode
 * @package sorokinmedia\user\tests\entities\SmsCode
 */
class SmsCode extends AbstractSmsCode
{
    use RelationClassTrait;

    public const TYPE_VERIFY = 1;

    /**
     * @param int|null $type_id
     * @return array|mixed
     */
    public static function getTypes(int $type_id = null)
    {
        $types = [
            self::TYPE_VERIFY => Yii::t('app', 'Верификация'),
        ];
        if ($type_id !== null) {
            return $types[$type_id];
        }
        return $types;
    }

    /**
     * @return bool
     */
    public function sendCode(): bool
    {
        return true;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        switch ($this->type_id) {
            case self::TYPE_VERIFY:
                return Yii::t('app', 'Код проверки {code}', [
                    'code' => $this->code
                ]);
            default:
                return '';
        }
    }

    /**
     * @return int
     * @throws Exception
     */
    public function generateCode(): int
    {
        return random_int(1000, 9999);
    }
}
