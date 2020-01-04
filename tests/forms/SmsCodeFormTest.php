<?php

namespace sorokinmedia\user\tests\forms;

use sorokinmedia\user\forms\SmsCodeForm;
use sorokinmedia\user\tests\entities\SmsCode\SmsCode;
use sorokinmedia\user\tests\TestCase;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 * Class SmsCodeFormTest
 * @package sorokinmedia\user\tests\forms
 */
class SmsCodeFormTest extends TestCase
{
    /**
     * @group forms
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testConstruct(): void
    {
        $this->initDb();
        $code = SmsCode::findOne(['user_id' => 1]);
        $form = new SmsCodeForm([], $code);
        $this->assertInstanceOf(SmsCodeForm::class, $form);
        $this->assertEquals($form->user_id, $code->user_id);
        $this->assertEquals($form->phone, $code->phone);
        $this->assertEquals($form->code, $code->code);
        $this->assertEquals($form->type_id, $code->type_id);
        $this->assertEquals($form->ip, $code->ip);
        $this->assertEquals($form->is_used, $code->is_used);
        $this->assertEquals($form->is_validated, $code->is_validated);
        $this->assertEquals($form->is_deleted, $code->is_deleted);
    }
}
