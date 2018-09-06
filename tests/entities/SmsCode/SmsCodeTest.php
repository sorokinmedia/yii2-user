<?php
namespace sorokinmedia\user\tests\entities\User;

use sorokinmedia\user\entities\{
    User\UserInterface,UserMeta\json\UserMetaPhone
};
use sorokinmedia\user\forms\SmsCodeForm;
use sorokinmedia\user\tests\entities\SmsCode\SmsCode;
use sorokinmedia\user\tests\TestCase;

/**
 * Class SmsCodeTest
 * @package sorokinmedia\user\tests\entities\User
 */
class SmsCodeTest extends TestCase
{
    /**
     * Сверяет поля в AR модели
     * @group sms-code
     */
    public function testFields()
    {
        $this->initDb();
        $user = new SmsCode();
        $this->assertEquals(
            [
                'id',
                'user_id',
                'phone',
                'created_at',
                'code',
                'type_id',
                'ip',
                'is_used',
                'is_validated',
                'is_deleted',
            ],
            array_keys($user->getAttributes())
        );
    }

    /**
     * проверяет наличие связей
     * @group sms-code
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testRelations()
    {
        $this->initDb();
        $code = SmsCode::findOne(['code' => 3244]);
        $this->assertInstanceOf(UserInterface::class, $code->user);
    }

    /**
     * @group sms-code
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testConstruct()
    {
        $this->initDb();
        $code = SmsCode::findOne(['code' => 3244]);
        $form = new SmsCodeForm([], $code);
        $code->form = $form;
        $this->assertInstanceOf(SmsCodeForm::class, $code->form);
    }

    /**
     * @group sms-code
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGetFromForm()
    {
        $this->initDb();
        $code = SmsCode::findOne(['code' => 3244]);
        $form = new SmsCodeForm([], $code);
        $new_code = new SmsCode([], $form);
        $new_code->getFromForm();
        $this->assertEquals($form->user_id, $new_code->user_id);
        $this->assertEquals($form->phone, $new_code->phone);
        $this->assertEquals($form->code, $new_code->code);
        $this->assertEquals($form->type_id, $new_code->type_id);
        $this->assertEquals($form->ip, $new_code->ip);
        $this->assertEquals($form->is_used, $new_code->is_used);
        $this->assertEquals($form->is_validated, $new_code->is_validated);
        $this->assertEquals($form->is_deleted, $new_code->is_deleted);
    }

    /**
     * @group sms-code
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testInsertModel()
    {
        $this->initDb();
        $code = SmsCode::findOne(['code' => 3244]);
        $form = new SmsCodeForm([], $code);
        $new_code = new SmsCode([], $form);
        $new_code->insertModel();
        $new_code->refresh();
        $this->assertNotEquals($code->id, $new_code->id);
        $this->assertEquals($form->user_id, $new_code->user_id);
        $this->assertEquals($form->phone, $new_code->phone);
        $this->assertEquals($form->code, $new_code->code);
        $this->assertEquals($form->type_id, $new_code->type_id);
        $this->assertEquals($form->ip, $new_code->ip);
        $this->assertEquals($form->is_used, $new_code->is_used);
        $this->assertEquals($form->is_validated, $new_code->is_validated);
        $this->assertEquals($form->is_deleted, $new_code->is_deleted);
    }

    /**
     * @group sms-code
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testUpdateModel()
    {
        $this->initDb();
        $code = SmsCode::findOne(['code' => 3244]);
        $form = new SmsCodeForm([
            'code' => 7555,
            'is_used' => 1,
            'is_validated' => 1,
            'is_deleted' => 1,
        ], $code);
        $code->form = $form;
        $code->updateModel();
        $code->refresh();
        $this->assertEquals(7555, $code->code);
        $this->assertEquals(1, $code->is_used);
        $this->assertEquals(1, $code->is_validated);
        $this->assertEquals(1, $code->is_deleted);
    }

    /**
     * @group sms-code
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testDeleteModel()
    {
        $this->initDb();
        $code = SmsCode::findOne(['code' => 3244]);
        $code->deleteModel();
        $code->refresh();
        $this->assertEquals(1, $code->is_deleted);
    }

    /**
     * @group sms-code
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGetTypes()
    {
        $this->initDb();
        $types = SmsCode::getTypes();
        $this->assertInternalType('array', $types);
        $this->assertNotEmpty($types);
        $this->assertEquals(\Yii::t('app', 'Верификация'), $types[SmsCode::TYPE_VERIFY]);

        $type = SmsCode::getTypes(SmsCode::TYPE_VERIFY);
        $this->assertEquals(\Yii::t('app', 'Верификация'), $type);
    }

    /**
     * @group sms-code
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGetType()
    {
        $this->initDb();
        $code = SmsCode::findOne(['code' => 3244]);
        $type = $code->getType();
        $this->assertEquals(\Yii::t('app', 'Верификация'), $type);
    }

    /**
     * @group sms-code
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGenerateCode()
    {
        $this->initDb();
        $code = SmsCode::findOne(['code' => 3244]);
        $old_code = $code->code;
        $new_code = $code->generateCode();
        $this->assertNotEquals($old_code, $new_code);
        $this->assertInternalType('integer', $new_code);
    }

    /**
     * @group sms-code
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGetMessage()
    {
        $this->initDb();
        $code = SmsCode::findOne(['code' => 3244]);
        $message = $code->getMessage();
        $this->assertInternalType('string', $message);
        $this->assertEquals((\Yii::t('app', 'Код проверки {code}', [
            'code' => $code->code
        ])), $message);
    }

    /**
     * @group sms-code
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testSendCode()
    {
        $this->initDb();
        $code = SmsCode::findOne(['code' => 3244]);
        $this->assertTrue($code->sendCode());
    }

    /**
     * @group sms-code
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGetCodeByUser()
    {
        $this->initDb();
        $user = User::findOne(1);
        $code = SmsCode::getCodeByUser($user, SmsCode::TYPE_VERIFY);
        $this->assertInstanceOf(SmsCode::class, $code);
    }

    /**
     * @group sms-code
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGetCodeByIp()
    {
        $this->initDb();
        $code = SmsCode::getCodeByIp('109.124.226.156', SmsCode::TYPE_VERIFY);
        $this->assertInstanceOf(SmsCode::class, $code);
    }

    /**
     * @group sms-code
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGetRequestedTodayByIp()
    {
        $this->initDb();
        $this->initDbAdditional();
        $codes_count = SmsCode::getRequestedTodayByIp('109.124.226.156', SmsCode::TYPE_VERIFY);
        $this->assertEquals(1, $codes_count);
    }

    /**
     * @group sms-code
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGetRequestedTodayByUser()
    {
        $this->initDb();
        $this->initDbAdditional();
        $user = User::findOne(1);
        $codes_count = SmsCode::getRequestedTodayByUser($user, SmsCode::TYPE_VERIFY);
        $this->assertEquals(1, $codes_count);
    }

    /**
     * @group sms-code
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGetRequestedTodayForUser()
    {
        $this->initDb();
        $this->initDbAdditional();
        $user = User::findOne(1);
        $codes = SmsCode::getRequestedTodayForUser($user);
        $this->assertNotEmpty($codes);
        $this->assertInstanceOf(SmsCode::class, $codes[0]);
        $this->assertEquals(1, count($codes));
    }

    /**
     * @group sms-code
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testResetLimit()
    {
        $this->initDb();
        $this->initDbAdditional();
        $user = User::findOne(1);
        $this->assertTrue(SmsCode::resetLimit($user));
        $codes = SmsCode::getRequestedTodayForUser($user);
        $this->assertEmpty($codes);
        $codes_count = SmsCode::getRequestedTodayByUser($user, SmsCode::TYPE_VERIFY);
        $this->assertEquals(0, $codes_count);
    }

    /**
     * @group sms-code
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testCheckUser()
    {
        $this->initDb();
        $this->initDbAdditional();
        $code = SmsCode::findOne(['code' => 3244]);
        $this->assertTrue($code->checkUse());
        $this->assertEquals(1, $code->is_used);
        $this->assertEquals(0, $code->is_validated);
        $this->assertTrue($code->checkUse(true));
        $this->assertEquals(2, $code->is_used);
        $this->assertEquals(1, $code->is_validated);
    }

    /**
     * @group sms-code
     */
    public function testPhoneFormatter()
    {
        $phone = new UserMetaPhone([
            'country' => 7,
            'number' => 9198078281
        ]);
        $formatted = SmsCode::phoneFormatter($phone);
        $this->assertEquals('+7(919)807-82-81', $formatted);
    }
}