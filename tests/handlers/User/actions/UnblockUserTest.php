<?php
namespace sorokinmedia\user\tests\handlers\User\actions;

use sorokinmedia\user\handlers\User\UserHandler;
use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;

/**
 * Class UnblockUserTest
 * @package sorokinmedia\user\tests\handlers\User\actions
 */
class UnblockUserTest extends TestCase
{
    /**
     * @group user-handler
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function testHandler()
    {
        $this->initDb();
        $user = User::findOne(1);
        $user->blockUser();
        $handler = new UserHandler($user);
        $this->assertTrue($handler->unblock());
        $user->refresh();
        $this->assertEquals(User::STATUS_ACTIVE, $user->status_id);
    }
}