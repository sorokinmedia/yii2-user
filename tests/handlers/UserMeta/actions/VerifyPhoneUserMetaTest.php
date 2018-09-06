<?php
namespace sorokinmedia\user\tests\handlers\UserMeta\actions;

use sorokinmedia\user\entities\UserMeta\json\UserMetaPhone;
use sorokinmedia\user\forms\UserMetaForm;
use sorokinmedia\user\handlers\UserMeta\UserMetaHandler;
use sorokinmedia\user\tests\entities\UserMeta\UserMeta;
use sorokinmedia\user\tests\TestCase;

/**
 * Class UpdateUserMetaTest
 * @package sorokinmedia\user\tests\handlers\UserMeta\actions
 *
 * тестирование action update
 */
class SetPhoneUserMetaTest extends TestCase
{
    /**
     * @group user-meta-handler
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testAction()
    {
        $this->initDb();
        $user_meta = UserMeta::findOne(['user_id' => 1]);
        $phone = new UserMetaPhone([
            'country' => 7,
            'number' => 9172298129,
            'is_verified' => false
        ]);
        $handler = new UserMetaHandler($user_meta);
        $this->assertTrue($handler->setPhone($phone));
        $user_meta->refresh();
        $this->assertEquals('{"country":7,"number":9172298129,"is_verified":false}', $user_meta->notification_phone);
    }
}