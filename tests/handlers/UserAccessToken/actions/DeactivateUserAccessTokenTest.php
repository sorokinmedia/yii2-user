<?php
namespace sorokinmedia\user\tests\handlers\UserAccessToken\actions;

use sorokinmedia\user\handlers\UserAccessToken\UserAccessTokenHandler;
use sorokinmedia\user\tests\entities\UserAccessToken\UserAccessToken;
use sorokinmedia\user\tests\TestCase;

/**
 * Class DeactivateUserAccessTokenTest
 * @package sorokinmedia\user\tests\handlers\UserAccessToken\actions
 *
 * тестирование action deactivate
 */
class DeactivateUserAccessTokenTest extends TestCase
{
    /**
     * @group user-access-token-handler
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testDeactivate()
    {
        $this->initDb();
        $token = UserAccessToken::findOne(['user_id' => 1, 'is_active' => 1]);
        $this->assertTrue((new UserAccessTokenHandler($token))->deactivate());
        $token->refresh();
        $this->assertEquals(0, $token->is_active);
    }
}