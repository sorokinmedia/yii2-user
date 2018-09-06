<?php
namespace sorokinmedia\user\tests\forms;

use sorokinmedia\helpers\DateHelper;
use sorokinmedia\user\entities\User\AbstractUser;
use sorokinmedia\user\entities\User\UserInterface;
use sorokinmedia\user\forms\PasswordResetRequestForm;
use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;
use yii\db\Connection;
use yii\db\Schema;

/**
 * Class PasswordChangeFormTest
 * @package sorokinmedia\user\tests\forms
 *
 * тест формы смены пароля
 */
class PasswordResetRequestFormTest extends TestCase
{
    /**
     * @group forms
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function testConstruct()
    {
        $this->initDb();
        $user = User::findOne(1);
        $form = new PasswordResetRequestForm([
            'email' => 'test@yandex.ru',
            'password_reset_token_expire' => DateHelper::TIME_HOUR_ONE
        ]);
        $form->setUser($user);
        $this->assertInstanceOf(PasswordResetRequestForm::class, $form);
        $this->assertEquals($form->email, 'test@yandex.ru');
        $this->assertInstanceOf(UserInterface::class, $form->getUser());
    }

    /**
     * @group forms
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testSendEmail()
    {
        $this->initDb();
        $user = User::findOne(1);
        $form = new PasswordResetRequestForm([
            'email' => 'test@yandex.ru',
            'password_reset_token_expire' => DateHelper::TIME_HOUR_ONE
        ]);
        $form->setUser($user);
        $this->assertTrue($form->sendEmail());
        $user->refresh();
        $this->assertNotNull($user->email_confirm_token);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function initDb()
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
            'password_reset_token' => 'test_token',
            'auth_key' => 'NdLufkTZDHMPH8Sw3p5f7ukUXSXllYwM',
            'username' => 'IvanSidorov',
            'status_id' => AbstractUser::STATUS_WAIT_EMAIL,
            'created_at' => 1460902430,
            'last_entering_date' => 1532370359,
            'email_confirm_token' => 'vzixa24PHbxmz0RXeGaRys1IOuPzyiXq',
        ])->execute();
    }
}