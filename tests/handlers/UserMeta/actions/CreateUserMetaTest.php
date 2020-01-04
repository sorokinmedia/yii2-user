<?php

namespace sorokinmedia\user\tests\handlers\UserMeta\actions;

use sorokinmedia\user\handlers\UserMeta\UserMetaHandler;
use sorokinmedia\user\tests\entities\UserMeta\UserMeta;
use sorokinmedia\user\tests\TestCase;
use Throwable;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 * Class CreateUserMetaTest
 * @package sorokinmedia\user\tests\handlers\UserMeta\actions
 *
 * тестирование action create
 */
class CreateUserMetaTest extends TestCase
{
    /**
     * @group user-meta-handler
     * @throws Throwable
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testAction(): void
    {
        $this->initDb();
        $this->initDbAdditional();
        $user_meta = new UserMeta([
            'user_id' => 2
        ]);
        $this->assertTrue((new UserMetaHandler($user_meta))->create());
        $user_meta->refresh();
        $this->assertEquals(2, $user_meta->user_id);
        $this->assertInstanceOf(UserMeta::class, $user_meta);
    }
}
