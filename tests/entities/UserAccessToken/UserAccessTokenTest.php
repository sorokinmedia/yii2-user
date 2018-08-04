<?php
namespace sorokinmedia\user\tests\entities\UserAccessToken;

use sorokinmedia\user\entities\User\UserInterface;
use sorokinmedia\user\entities\UserAccessToken\UserAccessTokenInterface;
use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;
use yii\db\Connection;
use yii\db\Schema;

/**
 * Class UserAccessTokenTest
 * @package sorokinmedia\user\tests\entities\User
 */
class UserAccessTokenTest extends TestCase
{
    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testFields()
    {
        $this->initDb();
        $user_access_token = new UserAccessToken();
        $this->assertEquals(
            [
                'user_id',
                'access_token',
                'created_at',
                'updated_at',
                'expired_at',
                'is_active'
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
        $user_access_token = UserAccessToken::findOne(['user_id' => 1]);
        $this->assertInstanceOf(UserAccessTokenInterface::class, $user_access_token);
        $this->assertInstanceOf(UserInterface::class, $user_access_token->getUser()->one());
    }

    /**
     *
     */
    public function testGenerateToken()
    {
        $token = null;
        $token = UserAccessToken::generateToken('test_string');
        $this->assertNotNull($token);
        $this->assertInternalType('string', $token);
        $this->assertEquals(32, mb_strlen($token));
    }

    /**
     *
     */
    public function testGenerateExpired()
    {
        $time = time();
        $expired = UserAccessToken::generateExpired(false);
        $this->assertGreaterThanOrEqual($time + (60 * 60 * 24), $expired);
        $time_month = time();
        $expired_month = UserAccessToken::generateExpired(true);
        $this->assertGreaterThanOrEqual($time_month + (60 * 60 * 24 * 30), $expired_month);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testDeactivate()
    {
        $this->initDb();
        $token = UserAccessToken::findOne(['user_id' => 1]);
        $this->assertTrue($token->deactivate());
        $token->refresh();
        $time = time();
        $this->assertEquals(0, $token->is_active);
        $this->assertLessThanOrEqual($time, $token->expired_at);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testCreate()
    {
        $this->initDb();
        $user = User::findOne(1);
        $time = time();
        $token = UserAccessToken::create($user, true);
        $this->assertInstanceOf(UserAccessTokenInterface::class, $token);
        $this->assertEquals(1, $token->is_active);
        $this->assertEquals(1, $token->user_id);
        $this->assertEquals(32, mb_strlen($token->access_token));
        $this->assertGreaterThanOrEqual($time + (60 * 60 * 24 * 30), $token->expired_at);
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

        if ($db->getTableSchema('user_access_token')){
            $db->createCommand()->dropTable('user_access_token')->execute();
        }
        $db->createCommand()->createTable('user_access_token', [
            'user_id' => Schema::TYPE_INTEGER,
            'access_token' => Schema::TYPE_STRING . '(32) NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . '(11)',
            'updated_at' => Schema::TYPE_INTEGER . '(11)',
            'expired_at' => Schema::TYPE_INTEGER . '(11)',
            'is_active' => Schema::TYPE_TINYINT,
            'PRIMARY KEY(user_id, access_token)',
        ])->execute();
        $db->createCommand()->insert('user_access_token', [
            'user_id' => 1,
            'access_token' => 'a188dd6d0a16071691c0a6247ed76ed4',
            'created_at' => 1528365638,
            'updated_at' => null,
            'expired_at' => null,
            'is_active' => 1,
        ])->execute();
    }
}