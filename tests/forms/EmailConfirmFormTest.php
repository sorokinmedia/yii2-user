<?php
namespace sorokinmedia\user\tests\forms;

use sorokinmedia\user\entities\User\AbstractUser;
use sorokinmedia\user\forms\EmailConfirmForm;
use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;
use yii\db\Connection;
use yii\db\Schema;

/**
 * Class EmailConfirmFormTest
 * @package sorokinmedia\user\tests\forms
 *
 * тест формы подтверждения e-mail
 */
class EmailConfirmFormTest extends TestCase
{
    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function testConstruct()
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
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testConfirmEmail()
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
            'status_id' => 2,
            'created_at' => 1460902430,
            'last_entering_date' => 1532370359,
            'email_confirm_token' => 'vzixa24PHbxmz0RXeGaRys1IOuPzyiXq',
        ])->execute();
    }
}