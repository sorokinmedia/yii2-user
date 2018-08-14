<?php
namespace sorokinmedia\user\tests\entities\UserMeta;

use sorokinmedia\user\entities\User\UserInterface;
use sorokinmedia\user\forms\UserMetaForm;
use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;
use yii\db\Connection;
use yii\db\Schema;

/**
 * Class UserMetaTest
 * @package sorokinmedia\user\tests\entities\UserAccessToken
 */
class UserMetaTest extends TestCase
{
    /**
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
                'display_name',
                'tz',
                'location',
                'about',
            ],
            array_keys($user_access_token->getAttributes())
        );
    }

    /**
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
            'notification_phone' => 'form_phone',
            'full_name' => 'form_name',
            'tz' => 'Europe/Moscow',
            'location' => 'Europe/Moscow',
            'about' => 'form_location'
        ]);
        $user_meta->form = $form;
        $this->assertInstanceOf(UserMetaForm::class, $user_meta->form);
        $user_meta->getFromForm();
        $this->assertEquals($form->notification_email, $user_meta->notification_email);
        $this->assertEquals($form->notification_phone, $user_meta->notification_phone);
        $this->assertEquals($form->full_name, $user_meta->full_name);
        $this->assertEquals($form->tz, $user_meta->tz);
        $this->assertEquals($form->location, $user_meta->location);
        $this->assertEquals($form->about, $user_meta->about);
    }

    /**
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
        $this->assertEquals('+79198078281', $user_meta->notification_phone);
        $this->assertEquals(12345678, $user_meta->notification_telegram);
        $this->assertEquals('Вася Пупкин', $user_meta->full_name);
        $this->assertEquals('Вася Пупкин', $user_meta->display_name);
        $this->assertEquals('Europe/Samara', $user_meta->tz);
        $this->assertEquals('Russia/Samara', $user_meta->location);
        $this->assertEquals( 'О себе: текст', $user_meta->about);
    }

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
        $this->assertEquals('IvanSidorov', $user_meta->display_name);
        $this->assertEquals('Europe/Moscow', $user_meta->tz);
        $this->assertNull($user_meta->location);
        $this->assertNull( $user_meta->about);
    }

    /**
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
            'notification_phone' => 987654321,
            'full_name' => 'test_create_fullname',
            'tz' => 'Europe/London',
            'location' => 'UK/London',
            'about' => 'test_create_about'
        ]);
        $user_meta->form = $form;
        $user_meta->updateModel();
        $user_meta->refresh();
        $this->assertEquals('test_create@yandex.ru', $user_meta->notification_email);
        $this->assertEquals(987654321, $user_meta->notification_phone);
        $this->assertEquals('test_create_fullname', $user_meta->full_name);
        $this->assertEquals('test_create_fullname', $user_meta->display_name);
        $this->assertEquals('Europe/London', $user_meta->tz);
        $this->assertEquals('UK/London', $user_meta->location);
        $this->assertEquals( 'test_create_about', $user_meta->about);
    }

    /**
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
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    private function initDb()
    {
        @unlink(__DIR__ . '/runtime/sqlite.db');
        $db = new Connection([
            'dsn' => 'sqlite:' . \Yii::$app->getRuntimePath() . '/sqlite.db',
            'charset' => 'utf8',
        ]);
        \Yii::$app->set('db', $db);
        if ($db->getTableSchema('user')){
            $db->createCommand()->dropTable('user')->execute();
        }
        $db->createCommand()->createTable('user', [
            'id' => Schema::TYPE_PK,
            'email' => Schema::TYPE_STRING . '(255) NOT NULL',
            'password_hash' => Schema::TYPE_STRING . '(60) NOT NULL',
            'password_reset_token' =>Schema::TYPE_STRING . '(255)',
            'auth_key' => Schema::TYPE_STRING . '(45)',
            'username' => Schema::TYPE_STRING . '(255) NOT NULL',
            'status_id' => Schema::TYPE_TINYINT,
            'created_at' => Schema::TYPE_INTEGER . '(11)',
            'last_entering_date' => Schema::TYPE_INTEGER . '(11)',
            'email_confirm_token' => Schema::TYPE_STRING . '(255)'
        ])->execute();
        $db->createCommand()->insert('user', [
            'id' => 1,
            'email' => 'test@yandex.ru',
            'password_hash' => '$2y$13$965KGf0VPtTcQqflsIEDtu4kmvM4mstARSbtRoZRiwYZkUqCQWmcy',
            'password_reset_token' => null,
            'auth_key' => 'NdLufkTZDHMPH8Sw3p5f7ukUXSXllYwM',
            'username' => 'IvanSidorov',
            'status_id' => 1,
            'created_at' => 1460902430,
            'last_entering_date' => 1532370359,
            'email_confirm_token' => null,
        ])->execute();

        if ($db->getTableSchema('user_meta')){
            $db->createCommand()->dropTable('user_meta')->execute();
        }
        $db->createCommand()->createTable('user_meta', [
            'user_id' => Schema::TYPE_INTEGER,
            'notification_email' => Schema::TYPE_STRING . '(255)',
            'notification_phone' => Schema::TYPE_INTEGER . '(255)',
            'notification_telegram' => Schema::TYPE_INTEGER,
            'full_name' => Schema::TYPE_STRING . '(255)',
            'display_name' => Schema::TYPE_STRING . '(255)',
            'tz' => Schema::TYPE_STRING . '(100)',
            'location' => Schema::TYPE_STRING . '(200)',
            'about' => Schema::TYPE_TEXT,
            'PRIMARY KEY(user_id)',
        ])->execute();
        $db->createCommand()->insert('user_meta', [
            'user_id' => 1,
            'notification_email' => 'test1@yandex.ru',
            'notification_phone' => '+79198078281',
            'notification_telegram' => 12345678,
            'full_name' => 'Вася Пупкин',
            'display_name' => 'Вася Пупкин',
            'tz' => 'Europe/Samara',
            'location' => 'Russia/Samara',
            'about' => 'О себе: текст',
        ])->execute();
    }
}