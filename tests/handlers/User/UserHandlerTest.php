<?php
namespace sorokinmedia\user\tests\handlers\User;

use sorokinmedia\user\handlers\User\UserHandler;
use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;

/**
 * Class UserHandlerTest
 * @package sorokinmedia\user\tests\handlers\User
 *
 * тестирование хендлера User
 */
class UserHandlerTest extends TestCase
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
        $this->assertInstanceOf(UserHandler::class, $handler);
        $this->assertInstanceOf(User::class, $handler->user);
    }
}