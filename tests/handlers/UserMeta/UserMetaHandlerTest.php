<?php
namespace sorokinmedia\user\tests\handlers\UserMeta;

use sorokinmedia\user\handlers\UserMeta\UserMetaHandler;
use sorokinmedia\user\tests\entities\UserMeta\UserMeta;
use sorokinmedia\user\tests\TestCase;

/**
 * Class UserMetaHandlerTest
 * @package sorokinmedia\user\tests\handlers\UserMeta
 *
 * тестирование хегдлера UserMeta
 */
class UserMetaHandlerTest extends TestCase
{
    /**
     * @group user-meta-handler
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function testHandler()
    {
        $this->initDb();
        $user_meta = UserMeta::findOne(['user_id' =>1]);
        $handler = new UserMetaHandler($user_meta);
        $this->assertInstanceOf(UserMetaHandler::class, $handler);
        $this->assertInstanceOf(UserMeta::class, $handler->user_meta);
    }
}