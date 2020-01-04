<?php

namespace sorokinmedia\user\tests\handlers\UserAccessToken;

use sorokinmedia\user\handlers\UserAccessToken\UserAccessTokenHandler;
use sorokinmedia\user\tests\entities\UserAccessToken\UserAccessToken;
use sorokinmedia\user\tests\TestCase;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 * Class UserAccessTokenHandlerTest
 * @package sorokinmedia\user\tests\handlers\UserAccessToken
 *
 * тестирование хендлера UserAccessToken
 */
class UserAccessTokenHandlerTest extends TestCase
{
    /**
     * @group user-access-token-handler
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testHandler(): void
    {
        $this->initDb();
        $user_meta = UserAccessToken::findOne(['user_id' => 1]);
        $handler = new UserAccessTokenHandler($user_meta);
        $this->assertInstanceOf(UserAccessTokenHandler::class, $handler);
        $this->assertInstanceOf(UserAccessToken::class, $handler->token);
    }
}
