<?php
namespace sorokinmedia\user\tests\handlers\UserAccessToken;

use sorokinmedia\user\handlers\UserAccessToken\UserAccessTokenHandler;
use sorokinmedia\user\tests\entities\UserAccessToken\UserAccessToken;
use sorokinmedia\user\tests\TestCase;

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
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function testHandler()
    {
        $this->initDb();
        $user_meta = UserAccessToken::findOne(['user_id' =>1]);
        $handler = new UserAccessTokenHandler($user_meta);
        $this->assertInstanceOf(UserAccessTokenHandler::class, $handler);
        $this->assertInstanceOf(UserAccessToken::class, $handler->token);
    }
}