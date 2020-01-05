<?php

namespace sorokinmedia\user\tests\entities\User;

use RuntimeException;
use sorokinmedia\user\entities\User\UserInterface;
use sorokinmedia\user\entities\UserAccessToken\UserAccessTokenInterface;
use sorokinmedia\user\forms\SignupForm;
use sorokinmedia\user\forms\SignUpFormEmail;
use sorokinmedia\user\tests\TestCase;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\web\IdentityInterface;
use yii\web\ServerErrorHttpException;

/**
 * Class FormGeneratorTest
 * @package ma3obblu\gii\generators\tests\form
 *
 * тестирование генератора форм
 */
class UserTest extends TestCase
{
    /**
     * @throws InvalidConfigException
     * @throws Exception
     * Сверяет поля в AR модели
     */
    public function testFields(): void
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
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testRelations(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->assertInstanceOf(UserInterface::class, $user);
        $tokens = $user->getTokens()->all();
        $this->assertInstanceOf(UserAccessTokenInterface::class, $tokens[0]);
    }

    /**
     * @group user
     */
    public function testGetStatusesArray(): void
    {
        $this->assertEquals([
            User::STATUS_BLOCKED => Yii::t('app', 'Заблокирован'),
            User::STATUS_ACTIVE => Yii::t('app', 'Активен'),
            User::STATUS_WAIT_EMAIL => Yii::t('app', 'Ожидает подтверждения e-mail'),
            User::STATUS_MODERATION => Yii::t('app', 'На модерации'),
            User::STATUS_LANDING => Yii::t('app', 'С лендинга'),
        ], User::getStatusesArray());
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testGetStatus(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->assertEquals('Активен', $user->getStatus());
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testActiveDeactivate(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $user->activate();
        $this->assertEquals(User::STATUS_ACTIVE, $user->status_id);
        $user->deactivate();
        $this->assertEquals(User::STATUS_BLOCKED, $user->status_id);
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     * @throws \Exception
     */
    public function testBlockUnblock(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->assertTrue($user->blockUser());
        $user->refresh();
        $this->assertEquals(User::STATUS_BLOCKED, $user->status_id);
        $this->assertTrue($user->unblockUser());
        $user->refresh();
        $this->assertEquals(User::STATUS_ACTIVE, $user->status_id);
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     * @throws \Exception
     */
    public function testVerifyAccount(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $user->blockUser();
        $this->assertTrue($user->verifyAccount());
        $this->assertEquals(User::STATUS_ACTIVE, $user->status_id);
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testFindIdentity(): void
    {
        $this->initDb();
        $identity = User::findIdentity(1);
        $this->assertInstanceOf(IdentityInterface::class, $identity);
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testFindIdentityByAccessToken(): void
    {
        $this->initDb();
        $identity = User::findIdentityByAccessToken('NdLufkTZDHMPH8Sw3p5f7ukUXSXllYwM');
        $this->assertInstanceOf(IdentityInterface::class, $identity);

        $identity_at = User::findIdentityByAccessToken('a188dd6d0a16071691c0a6247ed76ed4');
        $this->assertInstanceOf(IdentityInterface::class, $identity_at);
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testGetAuthKey(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->assertEquals('NdLufkTZDHMPH8Sw3p5f7ukUXSXllYwM', $user->getAuthKey());
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testValidateAuthKey(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->assertTrue($user->validateAuthKey('NdLufkTZDHMPH8Sw3p5f7ukUXSXllYwM'));
        $this->assertFalse($user->validateAuthKey('false_key'));
    }

    /**
     * @group user
     * @throws \yii\base\Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testGeneratePasswordResetToken(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $user->generatePasswordResetToken();
        $this->assertNotNull($user->password_reset_token);
        $this->assertEquals(43, mb_strlen($user->password_reset_token));
    }

    /**
     * @group user
     * @throws \yii\base\Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testSaveGeneratedPasswordResetToken(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $old = $user->password_reset_token;
        $this->assertTrue($user->saveGeneratedPasswordResetToken());
        $this->assertNotEquals($user->password_reset_token, $old);
        $this->assertNotNull($user->password_reset_token);
    }

    /**
     * @group user
     * @throws \yii\base\Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testFindByPasswordResetToken(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Недействительный токен. Запросите сброс пароля еще раз.');
        $this->expectException(User::findByPasswordResetToken(3600, 'test_token'));
        $user->saveGeneratedPasswordResetToken();
        $founded_user = User::findByPasswordResetToken(3600, $user->password_reset_token);
        $this->assertInstanceOf(UserInterface::class, $founded_user);
        $this->assertEquals($user->id, $founded_user->id);
    }

    /**
     * @group user
     */
    public function testIsPasswordResetTokenValid(): void
    {
        $this->assertFalse(User::isPasswordResetTokenValid(3600));
        $this->assertFalse(User::isPasswordResetTokenValid(3600, 'P3LKsIDJLagliQS4yAMpcHEQFB0T_YwW_1529143969'));
        $this->assertTrue(User::isPasswordResetTokenValid(3600, 'P3LKsIDJLagliQS4yAMpcHEQFB0T_YwW_' . time()));
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testRemovePasswordResetToken(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $user->removePasswordResetToken();
        $this->assertNull($user->password_reset_token);
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testSendPasswordResetEmail(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->assertTrue($user->sendPasswordResetMail());
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testFindByEmail(): void
    {
        $this->initDb();
        $user = User::findByEmail('test@yandex.ru');
        $this->assertInstanceOf(UserInterface::class, $user);
    }

    /**
     * @group user
     * @throws \yii\base\Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testGenerateEmailConfirmToken(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $user->generateEmailConfirmToken();
        $this->assertNotNull($user->email_confirm_token);
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testRemoveEmailConfirmToken(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $user->removeEmailConfirmToken();
        $this->assertNull($user->email_confirm_token);
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testConfirmEmailAction(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $user->confirmEmailAction();
        $this->assertNull($user->email_confirm_token);
        $this->assertEquals(User::STATUS_ACTIVE, $user->status_id);
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testValidatePassword(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->assertFalse($user->validatePassword('test_password'));
        $this->assertTrue($user->validatePassword('another_test_password'));
    }

    /**
     * @group user
     * @throws \yii\base\Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testSetPassword(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $user->setPassword('test_password');
        $this->assertNotEquals('$2y$13$965KGf0VPtTcQqflsIEDtu4kmvM4mstARSbtRoZRiwYZkUqCQWmcy', $user->password_hash);
        $this->assertNotNull($user->password_hash);
    }

    /**
     * @group user
     * @throws \yii\base\Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testSaveNewPassword(): void
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
     * @group user
     * @throws \yii\base\Exception
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testGenerateAuthKey(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $user->auth_key = null;
        $user->generateAuthKey();
        $this->assertNotNull($user->auth_key);
    }

    /**
     * @group user
     */
    public function testGetRolesArray(): void
    {
        $roles = User::getRolesArray();
        $this->assertNotEmpty($roles);
        $this->assertEquals($roles[User::ROLE_ADMIN], Yii::t('app', 'Администратор'));
        $role = User::getRolesArray(User::ROLE_ADMIN);
        $this->assertEquals($role, Yii::t('app', 'Администратор'));
    }

    /**
     * @group user
     */
    public function testGetRoleLink(): void
    {
        $links = User::getRoleLink();
        $this->assertNotEmpty($links);
        $this->assertEquals($links[User::ROLE_ADMIN], 'admin');
        $link = User::getRoleLink(User::ROLE_ADMIN);
        $this->assertEquals($link, 'admin');
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testDeactivateTokens(): void
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
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testGetCheckToken(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->assertIsString($user->getCheckToken());
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testUpdateLastEntering(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $this->assertTrue($user->updateLastEntering());
        $this->assertEquals(time(), $user->last_entering_date);
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testAfterSignUp(): void
    {
        $this->initDb();
        $user = new User();
        $role = User::ROLE_ADMIN;
        $this->assertTrue($user->afterSignUp($role));
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testAfterSignUpEmail(): void
    {
        $this->initDb();
        $user = new User();
        $role = User::ROLE_ADMIN;
        $this->assertTrue($user->afterSignUpEmail($role));
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     * @throws ServerErrorHttpException
     */
    public function testSignUp(): void
    {
        $this->initDb();
        $user = new User();
        $signup_form = new SignupForm([
            'email' => 'Ma3oBblu@gmail.com',
            'username' => 'Ma3oBblu',
            'password' => 'test_password',
        ], $user, User::ROLE_ADMIN);
        $this->assertTrue($user->signUp($signup_form));
    }

    /**
     * @group user
     * @throws \yii\base\Exception
     * @throws InvalidConfigException
     * @throws Exception
     * @throws ServerErrorHttpException
     */
    public function testSignUpEmail(): void
    {
        $this->initDb();
        $user = new User();
        $signup_form = new SignUpFormEmail([
            'email' => 'Ma3oBblu@gmail.com',
        ], $user, User::ROLE_ADMIN);
        $signup_form->prepareUsernameAndPassword();
        $this->assertTrue($user->signUpEmail($signup_form));
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testSendEmailConfirmation(): void
    {
        $this->initDb();
        $user = new User();
        $this->assertTrue($user->sendEmailConfirmation());
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testSendEmailConfirmationWithPassword(): void
    {
        $this->initDb();
        $user = new User();
        $password = 'test_password';
        $this->assertTrue($user->sendEmailConfirmationWithPassword($password));
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testGetUsersArray(): void
    {
        $this->initDb();
        $array = User::getUsersArray();
        $this->assertNotNull($array);
        $this->assertEquals($array[1], 'IvanSidorov');
    }

    /**
     * @group user
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testGetActiveUsers(): void
    {
        $this->initDb();
        $models = User::getActiveUsers();
        $this->assertNotNull($models);
        $this->assertInstanceOf(UserInterface::class, $models[0]);
    }
}
