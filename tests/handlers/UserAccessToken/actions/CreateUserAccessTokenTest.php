<?php
namespace sorokinmedia\user\tests\handlers\UserAccessToken\actions;

use sorokinmedia\user\handlers\UserAccessToken\UserAccessTokenHandler;
use sorokinmedia\user\tests\entities\UserAccessToken\UserAccessToken;
use sorokinmedia\user\tests\TestCase;

/**
 * Class CreateUserAccessTokenTest
 * @package sorokinmedia\user\tests\handlers\UserAccessToken\actions
 *
 * тестирование action create
 */
class CreateUserAccessTokenTest extends TestCase
{
    /**
     * @group user-access-token-handler
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testCreate()
    {
        $this->initDb();
        $token = new UserAccessToken([
            'user_id' => 1,
            'access_token' => UserAccessToken::generateToken('test@yandex.ru'),
            'expired_at' => UserAccessToken::generateExpired(false),
            'is_active' => 0,
        ]);
        $this->assertTrue((new UserAccessTokenHandler($token))->create());
        $token->refresh();
        $this->assertEquals(1, $token->user_id);
        $this->assertNotNull($token->access_token);
        $this->assertEquals(0, $token->is_active);
    }
}