<?php

namespace sorokinmedia\user\tests\handlers\SmsCode;

use sorokinmedia\user\handlers\SmsCode\SmsCodeHandler;
use sorokinmedia\user\tests\entities\SmsCode\SmsCode;
use sorokinmedia\user\tests\TestCase;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 * Class SmsCodeHandlerTest
 * @package sorokinmedia\user\tests\handlers\User
 *
 * тестирование хендлера SmsCode
 */
class SmsCodeHandlerTest extends TestCase
{
    /**
     * @group sms-code-handler
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testHandler(): void
    {
        $this->initDb();
        $code = SmsCode::findOne(['user_id' => 1]);
        $handler = new SmsCodeHandler($code);
        $this->assertInstanceOf(SmsCodeHandler::class, $handler);
        $this->assertInstanceOf(SmsCode::class, $handler->sms_code);
    }
}
