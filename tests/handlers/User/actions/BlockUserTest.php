<?php
namespace sorokinmedia\user\tests\handlers\User\actions;

use sorokinmedia\user\handlers\User\UserHandler;
use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;

/**
 * Class BlockUserTest
 * @package sorokinmedia\user\tests\handlers\User\actions
 */
class BlockUserTest extends TestCase
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
        $handler = new UserHandler($user);
        $this->assertTrue($handler->block());
        $user->refresh();
        $this->assertEquals(User::STATUS_BLOCKED, $user->status_id);
    }
}