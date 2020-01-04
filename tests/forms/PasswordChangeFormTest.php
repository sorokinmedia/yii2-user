<?php

namespace sorokinmedia\user\tests\forms;

use sorokinmedia\user\entities\User\AbstractUser;
use sorokinmedia\user\forms\PasswordChangeForm;
use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\db\Exception;
use yii\db\Schema;
use yii\web\ServerErrorHttpException;

/**
 * Class PasswordChangeFormTest
 * @package sorokinmedia\user\tests\forms
 *
 * тест формы смены пароля
 */
class PasswordChangeFormTest extends TestCase
{
    /**
     * @group forms
     * @throws InvalidConfigException
     * @throws Exception
     * @throws ServerErrorHttpException
     */
    public function testConstruct(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $form = new PasswordChangeForm([
            'password' => 'new_password',
            'password_repeat' => 'new_password',
        ], $user);
        $this->assertInstanceOf(PasswordChangeForm::class, $form);
        $this->assertEquals($form->password, 'new_password');
        $this->assertEquals($form->password_repeat, 'new_password');
    }

    /**
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function initDb(): void
    {
        @unlink(__DIR__ . '/runtime/sqlite.db');
        $db = new Connection([
            'dsn' => 'sqlite:' . Yii::$app->getRuntimePath() . '/sqlite.db',
            'charset' => 'utf8',
        ]);
        Yii::$app->set('db', $db);
        if ($db->getTableSchema('user')) {
            $db->createCommand()->dropTable('user')->execute();
        }
        $db->createCommand()->createTable('user', [
            'id' => Schema::TYPE_PK,
            'email' => Schema::TYPE_STRING . '(255) NOT NULL',
            'password_hash' => Schema::TYPE_STRING . '(60) NOT NULL',
            'password_reset_token' => Schema::TYPE_STRING . '(255)',
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
            'password_reset_token' => 'test_token',
            'auth_key' => 'NdLufkTZDHMPH8Sw3p5f7ukUXSXllYwM',
            'username' => 'IvanSidorov',
            'status_id' => AbstractUser::STATUS_WAIT_EMAIL,
            'created_at' => 1460902430,
            'last_entering_date' => 1532370359,
            'email_confirm_token' => 'vzixa24PHbxmz0RXeGaRys1IOuPzyiXq',
        ])->execute();
        $db->createCommand()->insert('user', [
            'id' => 2,
            'email' => 'test@yandex.ru',
            'password_hash' => '$2y$13$965KGf0VPtTcQqflsIEDtu4kmvM4mstARSbtRoZRiwYZkUqCQWmcy',
            'password_reset_token' => null,
            'auth_key' => 'NdLufkTZDHMPH8Sw3p5f7ukUXSXllYwM',
            'username' => 'IvanSidorov',
            'status_id' => AbstractUser::STATUS_BLOCKED,
            'created_at' => 1460902430,
            'last_entering_date' => 1532370359,
            'email_confirm_token' => 'vzixa24PHbxmz0RXeGaRys1IOuPzyiXq',
        ])->execute();
        $db->createCommand()->insert('user', [
            'id' => 3,
            'email' => 'test@yandex.ru',
            'password_hash' => '$2y$13$965KGf0VPtTcQqflsIEDtu4kmvM4mstARSbtRoZRiwYZkUqCQWmcy',
            'password_reset_token' => null,
            'auth_key' => 'NdLufkTZDHMPH8Sw3p5f7ukUXSXllYwM',
            'username' => 'IvanSidorov',
            'status_id' => AbstractUser::STATUS_ACTIVE,
            'created_at' => 1460902430,
            'last_entering_date' => 1532370359,
            'email_confirm_token' => null,
        ])->execute();
    }

    /**
     * @group forms
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testCheckRepeatTrue(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $form = new PasswordChangeForm([
            'password' => 'new_password',
            'password_repeat' => 'new_password',
        ], $user);
        $this->assertTrue($form->checkRepeat());
    }

    /**
     * @group forms
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testCheckRepeatFalse(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $form = new PasswordChangeForm([
            'password' => 'new_password',
            'password_repeat' => 'new_password1',
        ], $user);
        $this->assertFalse($form->checkRepeat());
    }

    /**
     * @group forms
     * @throws \yii\base\Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testChangePassword(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $form = new PasswordChangeForm([
            'password' => 'new_password',
            'password_repeat' => 'new_password',
        ], $user);
        $this->assertTrue($form->checkRepeat());
        $old_password = $user->password_hash;
        $form->changePassword();
        $user->refresh();
        $this->assertNotEquals($old_password, $user->password_hash);
    }
}
