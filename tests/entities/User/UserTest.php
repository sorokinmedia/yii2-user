<?php
namespace sorokinmedia\user\tests\entities\User;

use sorokinmedia\user\entities\User\UserInterface;
use sorokinmedia\user\entities\UserAccessToken\UserAccessTokenInterface;
use sorokinmedia\user\forms\SignupForm;
use sorokinmedia\user\tests\TestCase;
use yii\db\Connection;
use yii\db\Schema;
use yii\web\IdentityInterface;

/**
 * Class FormGeneratorTest
 * @package ma3obblu\gii\generators\tests\form
 *
 * тестирование генератора форм
 */
class UserTest extends TestCase
{
    /**
     * Сверяет поля в AR модели
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
     * проверяет наличие связей
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testRelations()
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->assertInstanceOf(UserInterface::class, $user);
        $tokens = $user->getTokens()->all();
        $this->assertInstanceOf(UserAccessTokenInterface::class, $tokens[0]);
    }

    public function testGetStatusesArray()
    {
        $this->assertEquals([
            User::STATUS_BLOCKED => \Yii::t('app', 'Заблокирован'),
            User::STATUS_ACTIVE => \Yii::t('app','Активен'),
            User::STATUS_WAIT => \Yii::t('app','Ожидает подтверждения'),
        ], User::getStatusesArray());
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGetStatus()
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->assertEquals('Активен', $user->getStatus());
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testActiveDeactivate()
    {
        $this->initDb();
        $user = User::findOne(1);
        $user->activate();
        $this->assertEquals(User::STATUS_ACTIVE, $user->status_id);
        $user->deactivate();
        $this->assertEquals(User::STATUS_BLOCKED, $user->status_id);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testBlockUnblock()
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->assertTrue($user->blockUser());
        $user->refresh();
        $this->assertEquals(User::STATUS_ACTIVE, $user->status_id);
        $this->assertTrue($user->unblockUser());
        $user->refresh();
        $this->assertEquals(User::STATUS_BLOCKED, $user->status_id);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testFindIdentity()
    {
        $this->initDb();
        $identity = User::findIdentity(1);
        $this->assertInstanceOf(IdentityInterface::class, $identity);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testFindIdentityByAccessToken()
    {
        $this->initDb();
        $identity = User::findIdentityByAccessToken('NdLufkTZDHMPH8Sw3p5f7ukUXSXllYwM');
        $this->assertInstanceOf(IdentityInterface::class, $identity);

        $identity_at = User::findIdentityByAccessToken('a188dd6d0a16071691c0a6247ed76ed4');
        $this->assertInstanceOf(IdentityInterface::class, $identity_at);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGetAuthKey()
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->assertEquals('NdLufkTZDHMPH8Sw3p5f7ukUXSXllYwM', $user->getAuthKey());
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testValidateAuthKey()
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->assertTrue($user->validateAuthKey('NdLufkTZDHMPH8Sw3p5f7ukUXSXllYwM'));
        $this->assertFalse($user->validateAuthKey('false_key'));
    }

    /**
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGeneratePasswordResetToken()
    {
        $this->initDb();
        $user = User::findOne(1);
        $user->generatePasswordResetToken();
        $this->assertNotNull($user->password_reset_token);
        $this->assertEquals(43,mb_strlen($user->password_reset_token));
    }

    /**
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testSaveGeneratedPasswordResetToken()
    {
        $this->initDb();
        $user = User::findOne(1);
        $old = $user->password_reset_token;
        $this->assertTrue($user->saveGeneratedPasswordResetToken());
        $this->assertNotEquals($user->password_reset_token, $old);
        $this->assertNotNull($user->password_reset_token);
    }

    /**
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testFindByPasswordResetToken()
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->setExpectedException(\RuntimeException::class, 'Недействительный токен. Запросите сброс пароля еще раз.');
        $this->getExpectedException(User::findByPasswordResetToken(3600,'test_token'));
        $user->saveGeneratedPasswordResetToken();
        $founded_user = User::findByPasswordResetToken(3600, $user->password_reset_token);
        $this->assertInstanceOf(UserInterface::class, $founded_user);
        $this->assertEquals($user->id, $founded_user->id);
    }

    /**
     *
     */
    public function testIsPasswordResetTokenValid()
    {
        $this->assertFalse(User::isPasswordResetTokenValid(3600));
        $this->assertFalse(User::isPasswordResetTokenValid(3600, 'P3LKsIDJLagliQS4yAMpcHEQFB0T_YwW_1529143969'));
        $this->assertTrue(User::isPasswordResetTokenValid(3600, 'P3LKsIDJLagliQS4yAMpcHEQFB0T_YwW_' . time()));
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testRemovePasswordResetToken()
    {
        $this->initDb();
        $user = User::findOne(1);
        $user->removePasswordResetToken();
        $this->assertNull($user->password_reset_token);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testSendPasswordResetEmail()
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->assertTrue($user->sendPasswordResetMail());
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testFindByEmail()
    {
        $this->initDb();
        $user = User::findByEmail('test@yandex.ru');
        $this->assertInstanceOf(UserInterface::class, $user);
    }

    /**
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGenerateEmailConfirmToken()
    {
        $this->initDb();
        $user = User::findOne(1);
        $user->generateEmailConfirmToken();
        $this->assertNotNull($user->email_confirm_token);
    }
    
    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testRemoveEmailConfirmToken()
    {
        $this->initDb();
        $user = User::findOne(1);
        $user->removeEmailConfirmToken();
        $this->assertNull($user->email_confirm_token);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testConfirmEmailAction()
    {
        $this->initDb();
        $user = User::findOne(1);
        $user->confirmEmailAction();
        $this->assertNull($user->email_confirm_token);
        $this->assertEquals(User::STATUS_ACTIVE, $user->status_id);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testValidatePassword()
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->assertFalse($user->validatePassword('test_password'));
        $this->assertTrue($user->validatePassword('cytugxjks'));
    }

    /**
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testSetPassword()
    {
        $this->initDb();
        $user = User::findOne(1);
        $user->setPassword('test_password');
        $this->assertNotEquals('$2y$13$965KGf0VPtTcQqflsIEDtu4kmvM4mstARSbtRoZRiwYZkUqCQWmcy', $user->password_hash);
        $this->assertNotNull($user->password_hash);
    }

    /**
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testSaveNewPassword()
    {
        $this->initDb();
        $user = User::findOne(1);
        $old = $user->password_hash;
        $user->saveGeneratedPasswordResetToken();
        $this->assertTrue($user->saveNewPassword('test_password', $user->password_reset_token));
        $this->assertNull($user->password_reset_token);
        $this->assertNotEquals($old, $user->password_hash);
    }

    /**
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGenerateAuthKey()
    {
        $this->initDb();
        $user = User::findOne(1);
        $user->auth_key = null;
        $user->generateAuthKey();
        $this->assertNotNull($user->auth_key);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testDeactivateTokens()
    {
        $this->initDb();
        $user = User::findOne(1);
        $tokens = $user->getTokens()->all();
        $this->assertTrue($user->deactivateTokens());
        $new_tokens = $user->getTokens()->all();
        $this->assertNotEquals($tokens[0]->is_active, $new_tokens[0]->is_active);
        $this->assertEquals(0, $new_tokens[0]->is_active);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGetCheckToken()
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->assertInternalType('string', $user->getCheckToken());
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testAfterSignUp()
    {
        $this->initDb();
        $user = new User();
        $this->assertTrue($user->afterSignUp());
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function testSignUp()
    {
        $this->initDb();
        $user = new User();
        $signip_form = new SignupForm([
            'email' => 'Ma3oBblu@gmail.com',
            'username' => 'Ma3oBblu',
            'password' => 'test_password',
        ], $user);
        $this->assertTrue($user->signUp($signip_form));
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testSendEmailConfirmation()
    {
        $this->initDb();
        $user = new User();
        $this->assertTrue($user->sendEmailConfirmation());
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGetUsersArray()
    {
        $this->initDb();
        $array = User::getUsersArray();
        $this->assertNotNull($array);
        $this->assertEquals($array[1], 'IvanSidorov');
    }

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGetActiveUsers()
    {
        $this->initDb();
        $models = User::getActiveUsers();
        $this->assertNotNull($models);
        $this->assertInstanceOf(UserInterface::class, $models[0]);
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