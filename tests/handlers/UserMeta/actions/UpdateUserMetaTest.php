<?php
namespace sorokinmedia\user\tests\handlers\UserMeta\actions;

use sorokinmedia\user\forms\UserMetaForm;
use sorokinmedia\user\handlers\UserMeta\UserMetaHandler;
use sorokinmedia\user\tests\entities\UserMeta\UserMeta;
use sorokinmedia\user\tests\TestCase;
use yii\db\Connection;
use yii\db\Schema;

/**
 * Class UpdateUserMetaTest
 * @package sorokinmedia\user\tests\handlers\UserMeta\actions
 *
 * тестирование action update
 */
class UpdateUserMetaTest extends TestCase
{
    /**
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testUpdate()
    {
        $this->initDb();
        $user_meta = UserMeta::findOne(1);
        $user_meta_form = new UserMetaForm([], $user_meta);
        $user_meta_form->notification_email = 'test_email@yandex.ru';
        $user_meta_form->notification_phone = '79198078281';
        $user_meta_form->full_name = 'test_full_name';
        $user_meta_form->tz = 'Europe/London';
        $user_meta_form->location = 'Europe/London';
        $user_meta_form->about = 'test_about';
        $user_meta->form = $user_meta_form;
        $this->assertTrue((new UserMetaHandler($user_meta))->update());
        $user_meta->refresh();
        $this->assertEquals('test_email@yandex.ru', $user_meta->notification_email);
        $this->assertEquals('79198078281', $user_meta->notification_phone);
        $this->assertEquals('test_full_name', $user_meta->full_name);
        $this->assertEquals('Europe/London', $user_meta->tz);
        $this->assertEquals('Europe/London', $user_meta->location);
        $this->assertEquals('test_about', $user_meta->about);
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
        $db->createCommand()->insert('user', [
            'id' => 2,
            'email' => 'test2@yandex.ru',
            'password_hash' => '$2y$13$965KGf0VPtTcQqflsIEDtu4kmvM4mstARSbtRoZRiwYZkUqCQWmcy',
            'password_reset_token' => null,
            'auth_key' => 'NdLufkTZDHMPH8Sw3p5f7ukUXSXllYwM',
            'username' => 'VasyaPupkin',
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