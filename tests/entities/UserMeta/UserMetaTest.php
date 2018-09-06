<?php
namespace sorokinmedia\user\tests\entities\UserMeta;

use sorokinmedia\user\entities\UserMeta\json\UserMetaPhone;
use sorokinmedia\user\forms\UserMetaForm;
use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;
use yii\helpers\Json;

/**
 * Class UserMetaTest
 * @package sorokinmedia\user\tests\entities\UserAccessToken
 */
class UserMetaTest extends TestCase
{
    /**
     * @group user-meta
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testFields()
    {
        $this->initDb();
        $user_access_token = new UserMeta();
        $this->assertEquals(
            [
                'user_id',
                'notification_email',
                'notification_phone',
                'notification_telegram',
                'full_name',
                'tz',
                'location',
                'about',
                'custom_fields'
            ],
            array_keys($user_access_token->getAttributes())
        );
    }

    /**
     * @group user-meta
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testRelations()
    {
        $this->initDb();
        $user_meta = UserMeta::findOne(['user_id' => 1]);
        $this->assertInstanceOf(UserMeta::class, $user_meta);
        $this->assertInstanceOf(User::class, $user_meta->getUser()->one());
    }

    /**
     * @group user-meta
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGetFromForm()
    {
        $this->initDb();
        $user_meta = UserMeta::findOne(['user_id' => 1]);
        $this->assertInstanceOf(UserMeta::class, $user_meta);
        $form = new UserMetaForm([
            'notification_email' => 'form@yandex.ru',
            'full_name' => '{"name": "Руслан", "surname": "Гилязетдинов", "patronymic": "Рашидович"}',
            'tz' => 'Europe/Moscow',
            'location' => 'Europe/Moscow',
            'about' => 'form_location'
        ]);
        $user_meta->form = $form;
        $this->assertInstanceOf(UserMetaForm::class, $user_meta->form);
        $user_meta->getFromForm();
        $this->assertEquals($form->notification_email, $user_meta->notification_email);
        $this->assertEquals($form->full_name, $user_meta->full_name);
        $this->assertEquals($form->tz, $user_meta->tz);
        $this->assertEquals($form->location, $user_meta->location);
        $this->assertEquals($form->about, $user_meta->about);
    }

    /**
     * @group user-meta
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testCreateExisted()
    {
        $this->initDb();
        $user = User::findOne(1);
        /** @var UserMeta $user_meta */
        $user_meta = UserMeta::create($user);
        $this->assertInstanceOf(UserMeta::class, $user_meta);
        $this->assertEquals(1, $user_meta->user_id);
        $this->assertEquals('test1@yandex.ru', $user_meta->notification_email);
        $this->assertEquals('{"number": 9198078281, "country": 7, "is_verified": true}', $user_meta->notification_phone);
        $this->assertEquals(12345678, $user_meta->notification_telegram);
        $this->assertEquals('{"name": "Руслан", "surname": "Гилязетдинов", "patronymic": "Рашидович"}', $user_meta->full_name);
        $this->assertEquals('Europe/Samara', $user_meta->tz);
        $this->assertEquals('Russia/Samara', $user_meta->location);
        $this->assertEquals( 'О себе: текст', $user_meta->about);
        $this->assertEquals('[{"name": "Афвф", "value": "аывфыы 34"}]', $user_meta->custom_fields);
    }

    /**
     * @group user-meta
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\db\StaleObjectException
     */
    public function testCreateNew()
    {
        $this->initDb();
        $user = User::findOne(1);
        /** @var UserMeta $user_meta */
        $user_meta = UserMeta::findOne(1);
        $user_meta->delete();
        $user_meta->refresh();
        $user_meta = UserMeta::create($user);
        $this->assertInstanceOf(UserMeta::class, $user_meta);
        $this->assertEquals(1, $user_meta->user_id);
        $this->assertEquals('test@yandex.ru', $user_meta->notification_email);
        $this->assertNull($user_meta->notification_phone);
        $this->assertNull($user_meta->notification_telegram);
        $this->assertNull($user_meta->full_name);
        $this->assertEquals('Europe/Moscow', $user_meta->tz);
        $this->assertNull($user_meta->location);
        $this->assertNull( $user_meta->about);
    }

    /**
     * @group user-meta
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testInsertModel()
    {
        $this->initDb();
        $this->initDbAdditional();
        /** @var UserMeta $user_meta */
        $user_meta = new UserMeta([
            'user_id' => 2
        ]);
        $this->assertTrue($user_meta->insertModel());
    }

    /**
     * @group user-meta
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testUpdateModel()
    {
        $this->initDb();
        /** @var UserMeta $user_meta */
        $user_meta = UserMeta::findOne(1);
        $form = new UserMetaForm([
            'notification_email' => 'test_create@yandex.ru',
            'full_name' => '{"name": "Руслан", "surname": "Гилязетдинов", "patronymic": "Рашидович"}',
            'tz' => 'Europe/London',
            'location' => 'UK/London',
            'about' => 'test_create_about'
        ]);
        $user_meta->form = $form;
        $user_meta->updateModel();
        $user_meta->refresh();
        $this->assertEquals('test_create@yandex.ru', $user_meta->notification_email);
        $this->assertEquals('{"name": "Руслан", "surname": "Гилязетдинов", "patronymic": "Рашидович"}', $user_meta->full_name);
        $this->assertEquals('Europe/London', $user_meta->tz);
        $this->assertEquals('UK/London', $user_meta->location);
        $this->assertEquals( 'test_create_about', $user_meta->about);
    }

    /**
     * @group user-meta
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testSetTelegram()
    {
        $this->initDb();
        /** @var UserMeta $user_meta */
        $user_meta = UserMeta::findOne(1);
        $user_meta->setTelegram(987654321);
        $this->assertEquals(987654321, $user_meta->notification_telegram);
    }

    /**
     * @group user-meta
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGetTelegram()
    {
        $this->initDb();
        /** @var UserMeta $user_meta */
        $user_meta_telegram = UserMeta::getTelegram(12345678);
        $this->assertEquals(12345678, $user_meta_telegram);
    }

    /**
     * @group user-meta
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testSetPhone()
    {
        $this->initDb();
        $user_meta = UserMeta::findOne(['user_id' => 1]);
        $phone = new UserMetaPhone([
            'country' => 7,
            'number' => 9172298129,
            'is_verified' => false
        ]);
        $this->assertTrue($user_meta->setPhone($phone));
        $this->assertEquals('{"country":7,"number":9172298129,"is_verified":false}', $user_meta->notification_phone);
    }

    /**
     * @group user-meta
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testVerifyPhone()
    {
        $this->initDb();
        $user_meta = UserMeta::findOne(['user_id' => 1]);
        $phone = new UserMetaPhone([
            'country' => 7,
            'number' => 9172298129,
            'is_verified' => false
        ]);
        $this->assertTrue($user_meta->setPhone($phone));
        $this->assertTrue($user_meta->verifyPhone());
        $this->assertEquals('{"country":7,"number":9172298129,"is_verified":true}', $user_meta->notification_phone);
    }
}