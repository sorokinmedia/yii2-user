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
 * Class CreateSmsCodeTest
 * @package sorokinmedia\user\tests\handlers\User\actions
 */
class CreateSmsCodeTest extends TestCase
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
        $code = new SmsCode();
        $code_form = new SmsCodeForm([
            'user_id' => 1,
            'phone' => '79198078281',
            'code' => 6524,
            'type_id' => SmsCode::TYPE_VERIFY,
            'ip' => '109.124.226.156'
        ], $code);
        $code->form = $code_form;
        $handler = new SmsCodeHandler($code);
        $this->assertTrue($handler->create());
    }
}
