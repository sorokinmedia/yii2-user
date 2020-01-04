<?php

namespace sorokinmedia\user\tests\handlers\SmsCode\actions;

use sorokinmedia\user\handlers\SmsCode\SmsCodeHandler;
use sorokinmedia\user\tests\entities\SmsCode\SmsCode;
use sorokinmedia\user\tests\TestCase;
use Throwable;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 * Class DeleteSmsCodeTest
 * @package sorokinmedia\user\tests\handlers\SmsCode\actions
 */
class DeleteSmsCodeTest extends TestCase
{
    /**
     * @group sms-code-handler
     * @throws Throwable
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testHandler(): void
    {
        $this->initDb();
        $code = SmsCode::findOne(['user_id' => 1]);
        $handler = new SmsCodeHandler($code);
        $this->assertTrue($handler->delete());
        $code->refresh();
        $this->assertEquals(1, $code->is_deleted);
    }
}
