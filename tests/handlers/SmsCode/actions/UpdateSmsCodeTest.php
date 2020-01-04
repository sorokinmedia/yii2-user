<?php

namespace sorokinmedia\user\tests\handlers\SmsCode\actions;

use sorokinmedia\user\forms\SmsCodeForm;
use sorokinmedia\user\handlers\SmsCode\SmsCodeHandler;
use sorokinmedia\user\tests\entities\SmsCode\SmsCode;
use sorokinmedia\user\tests\TestCase;
use Throwable;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 * Class UpdateSmsCodeTest
 * @package sorokinmedia\user\tests\handlers\SmsCode\actions
 */
class UpdateSmsCodeTest extends TestCase
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
        $code_form = new SmsCodeForm([
            'user_id' => 1,
            'phone' => '79198078281',
            'code' => 6524,
            'type_id' => SmsCode::TYPE_VERIFY,
            'ip' => '109.124.226.156',
            'is_used' => 1
        ], $code);
        $code->form = $code_form;
        $handler = new SmsCodeHandler($code);
        $this->assertTrue($handler->update());
        $code->refresh();
        $this->assertEquals(1, $code->is_used);
        $this->assertEquals(6524, $code->code);
    }
}
