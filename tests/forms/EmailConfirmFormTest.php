<?php

namespace sorokinmedia\user\tests\forms;

use sorokinmedia\user\entities\User\AbstractUser;
use sorokinmedia\user\forms\EmailConfirmForm;
use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Connection;
use yii\db\Exception;
use yii\db\Schema;
use yii\web\ServerErrorHttpException;

/**
 * Class EmailConfirmFormTest
 * @package sorokinmedia\user\tests\forms
 *
 * тест формы подтверждения e-mail
 */
class EmailConfirmFormTest extends TestCase
{
    /**
     * @group forms
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testConstruct(): void
    {
        $this->initDb();
        $user = User::findOne(['email_confirm_token' => 'vzixa24PHbxmz0RXeGaRys1IOuPzyiXq']);
        $form = new EmailConfirmForm([
            'token' => 'vzixa24PHbxmz0RXeGaRys1IOuPzyiXq'
        ], $user);
        $this->assertInstanceOf(EmailConfirmForm::class, $form);
        $this->assertEquals($form->token, 'vzixa24PHbxmz0RXeGaRys1IOuPzyiXq');
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
            'password_reset_token' => null,
            'auth_key' => 'NdLufkTZDHMPH8Sw3p5f7ukUXSXllYwM',
            'username' => 'IvanSidorov',
            'status_id' => 2,
            'created_at' => 1460902430,
            'last_entering_date' => 1532370359,
            'email_confirm_token' => 'vzixa24PHbxmz0RXeGaRys1IOuPzyiXq',
        ])->execute();
    }

    /**
     * @group forms
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testConfirmEmail(): void
    {
        $this->initDb();
        $user = User::findOne(['email_confirm_token' => 'vzixa24PHbxmz0RXeGaRys1IOuPzyiXq']);
        $form = new EmailConfirmForm([
            'token' => 'vzixa24PHbxmz0RXeGaRys1IOuPzyiXq'
        ], $user);
        $this->assertTrue($form->confirmEmail());
        $user->refresh();
        $this->assertEquals(AbstractUser::STATUS_ACTIVE, $user->status_id);
        $this->assertNull($user->email_confirm_token);
    }
}
