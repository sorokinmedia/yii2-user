<?php

namespace sorokinmedia\user\tests\handlers\UserAccessToken\actions;

use sorokinmedia\user\handlers\UserAccessToken\UserAccessTokenHandler;
use sorokinmedia\user\tests\entities\UserAccessToken\UserAccessToken;
use sorokinmedia\user\tests\TestCase;
use Throwable;
use yii\base\InvalidConfigException;
use yii\db\Exception;

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
     * @throws Throwable
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testDeactivate(): void
    {
        $this->initDb();
        $token = UserAccessToken::findOne(['user_id' => 1, 'is_active' => 1]);
        $this->assertTrue((new UserAccessTokenHandler($token))->deactivate());
        $token->refresh();
        $this->assertEquals(0, $token->is_active);
    }
}
