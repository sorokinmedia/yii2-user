<?php
namespace sorokinmedia\user\tests\handlers\UserMeta\actions;

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
class UpdateUserMetaTest extends TestCase
{
    /**
     * @group user-meta-handler
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testUpdate()
    {
        $this->initDb();
        $user_meta = UserMeta::findOne(1);
        $user_meta_form = new UserMetaForm([], $user_meta);
        $user_meta_form->notification_email = 'test_email@yandex.ru';
        $user_meta_form->full_name = 'test_full_name';
        $user_meta_form->tz = 'Europe/London';
        $user_meta_form->location = 'Europe/London';
        $user_meta_form->about = 'test_about';
        $user_meta->form = $user_meta_form;
        $this->assertTrue((new UserMetaHandler($user_meta))->update());
        $user_meta->refresh();
        $this->assertEquals('test_email@yandex.ru', $user_meta->notification_email);
        $this->assertEquals('test_full_name', $user_meta->full_name);
        $this->assertEquals('Europe/London', $user_meta->tz);
        $this->assertEquals('Europe/London', $user_meta->location);
        $this->assertEquals('test_about', $user_meta->about);
    }
}