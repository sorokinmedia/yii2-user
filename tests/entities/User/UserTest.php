<?php
namespace sorokinmedia\user\tests\entities\User;

use sorokinmedia\user\tests\TestCase;
use yii\db\Connection;
use yii\db\Schema;

/**
 * Class FormGeneratorTest
 * @package ma3obblu\gii\generators\tests\form
 *
 * тестирование генератора форм
 */
class UserTest extends TestCase
{
    /**
     * @group billing
     * @group billing-entities
     * @group billing-entities-bill
     * @group billing-bill-all
     * Сверяет поля
     */
    public function testFields()
    {
        $this->initDb();
        $user = new User();
        $this->assertEquals(
            [
                'id',
                'email',
                'password_hash',
                'password_reset_token',
                'auth_key',
                'username',
                'status_id',
                'created_at',
                'last_entering_date',
                'email_confirm_token',
            ],
            array_keys($user->getAttributes())
        );
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
        if ($db->getTableSchema('user_access_token')){
            $db->createCommand()->dropTable('user_access_token')->execute();
        }
        $db->createCommand()->createTable('user_access_token', [
            'id' => Schema::TYPE_PK,
            'access_token' => Schema::TYPE_STRING . '(32) NOT NULL',
            'created_at' => Schema::TYPE_INTEGER . '(11)',
            'update_at' => Schema::TYPE_INTEGER . '(11)',
            'expired_at' => Schema::TYPE_INTEGER . '(11)',
            'is_active' => Schema::TYPE_TINYINT
        ])->execute();
    }
}