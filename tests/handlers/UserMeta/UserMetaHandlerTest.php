<?php

namespace sorokinmedia\user\tests\handlers\UserMeta;

use sorokinmedia\user\handlers\UserMeta\UserMetaHandler;
use sorokinmedia\user\tests\entities\UserMeta\UserMeta;
use sorokinmedia\user\tests\TestCase;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\web\ServerErrorHttpException;

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
     * @throws InvalidConfigException
     * @throws Exception
     * @throws ServerErrorHttpException
     */
    public function testHandler(): void
    {
        $this->initDb();
        $user_meta = UserMeta::findOne(['user_id' => 1]);
        $handler = new UserMetaHandler($user_meta);
        $this->assertInstanceOf(UserMetaHandler::class, $handler);
        $this->assertInstanceOf(UserMeta::class, $handler->user_meta);
    }
}
