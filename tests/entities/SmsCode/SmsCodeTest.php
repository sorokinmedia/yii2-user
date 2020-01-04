<?php

namespace sorokinmedia\user\tests\entities\User;

use sorokinmedia\user\entities\{User\UserInterface, UserMeta\json\UserMetaPhone};
use sorokinmedia\user\forms\SmsCodeForm;
use sorokinmedia\user\tests\entities\SmsCode\SmsCode;
use sorokinmedia\user\tests\TestCase;
use Throwable;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;

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
    public function testFields(): void
    {
        try {
            $this->initDb();
        } catch (InvalidConfigException $e) {

        } catch (Exception $e) {

        }
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
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testRelations(): void
    {
        $this->initDb();
        $code = SmsCode::findOne(['code' => 3244]);
        $this->assertInstanceOf(UserInterface::class, $code->user);
    }

    /**
     * @group sms-code
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testConstruct(): void
    {
        $this->initDb();
        $code = SmsCode::findOne(['code' => 3244]);
        $form = new SmsCodeForm([], $code);
        $code->form = $form;
        $this->assertInstanceOf(SmsCodeForm::class, $code->form);
    }

    /**
     * @group sms-code
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testGetFromForm(): void
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
     * @throws Throwable
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testInsertModel(): void
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
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testUpdateModel(): void
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
     * @throws InvalidConfigException
     * @throws Exception
     * @throws \Exception
     */
    public function testDeleteModel(): void
    {
        $this->initDb();
        $code = SmsCode::findOne(['code' => 3244]);
        $code->deleteModel();
        $code->refresh();
        $this->assertEquals(1, $code->is_deleted);
    }

    /**
     * @group sms-code
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testGetTypes(): void
    {
        $this->initDb();
        $types = SmsCode::getTypes();
        $this->assertIsArray($types);
        $this->assertNotEmpty($types);
        $this->assertEquals(Yii::t('app', 'Верификация'), $types[SmsCode::TYPE_VERIFY]);

        $type = SmsCode::getTypes(SmsCode::TYPE_VERIFY);
        $this->assertEquals(Yii::t('app', 'Верификация'), $type);
    }

    /**
     * @group sms-code
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testGetType(): void
    {
        $this->initDb();
        $code = SmsCode::findOne(['code' => 3244]);
        $type = $code->getType();
        $this->assertEquals(Yii::t('app', 'Верификация'), $type);
    }

    /**
     * @group sms-code
     * @throws InvalidConfigException
     * @throws Exception
     * @throws \Exception
     */
    public function testGenerateCode(): void
    {
        $this->initDb();
        $code = SmsCode::findOne(['code' => 3244]);
        $old_code = $code->code;
        $new_code = $code->generateCode();
        $this->assertNotEquals($old_code, $new_code);
        $this->assertIsInt($new_code);
    }

    /**
     * @group sms-code
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testGetMessage(): void
    {
        $this->initDb();
        $code = SmsCode::findOne(['code' => 3244]);
        $message = $code->getMessage();
        $this->assertIsString($message);
        $this->assertEquals((Yii::t('app', 'Код проверки {code}', [
            'code' => $code->code
        ])), $message);
    }

    /**
     * @group sms-code
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testSendCode(): void
    {
        $this->initDb();
        $code = SmsCode::findOne(['code' => 3244]);
        $this->assertTrue($code->sendCode());
    }

    /**
     * @group sms-code
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testGetCodeByUser(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $code = SmsCode::getCodeByUser($user, SmsCode::TYPE_VERIFY);
        $this->assertInstanceOf(SmsCode::class, $code);
    }

    /**
     * @group sms-code
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testGetCodeByIp(): void
    {
        $this->initDb();
        $code = SmsCode::getCodeByIp('109.124.226.156', SmsCode::TYPE_VERIFY);
        $this->assertInstanceOf(SmsCode::class, $code);
    }

    /**
     * @group sms-code
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testGetRequestedTodayByIp(): void
    {
        $this->initDb();
        $this->initDbAdditional();
        $codes_count = SmsCode::getRequestedTodayByIp('109.124.226.156', SmsCode::TYPE_VERIFY);
        $this->assertEquals(1, $codes_count);
    }

    /**
     * @group sms-code
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testGetRequestedTodayByUser(): void
    {
        $this->initDb();
        $this->initDbAdditional();
        $user = User::findOne(1);
        $codes_count = SmsCode::getRequestedTodayByUser($user, SmsCode::TYPE_VERIFY);
        $this->assertEquals(1, $codes_count);
    }

    /**
     * @group sms-code
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testGetRequestedTodayForUser(): void
    {
        $this->initDb();
        $this->initDbAdditional();
        $user = User::findOne(1);
        $codes = SmsCode::getRequestedTodayForUser($user);
        $this->assertNotEmpty($codes);
        $this->assertInstanceOf(SmsCode::class, $codes[0]);
        $this->assertCount(1, $codes);
    }

    /**
     * @group sms-code
     * @throws InvalidConfigException
     * @throws Exception
     * @throws \Exception
     */
    public function testResetLimit(): void
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
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testCheckUser(): void
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
    public function testPhoneFormatter(): void
    {
        $phone = new UserMetaPhone([
            'country' => 7,
            'number' => 9198078281
        ]);
        $formatted = SmsCode::phoneFormatter($phone);
        $this->assertEquals('+7(919)807-82-81', $formatted);
    }
}
