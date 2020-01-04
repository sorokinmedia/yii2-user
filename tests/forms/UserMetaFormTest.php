<?php

namespace sorokinmedia\user\tests\forms;

use sorokinmedia\user\forms\UserMetaForm;
use sorokinmedia\user\tests\entities\UserMeta\UserMeta;
use sorokinmedia\user\tests\TestCase;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 * Class UserMetaFormTest
 * @package sorokinmedia\user\tests\forms
 *
 * тест формы работы с метой
 */
class UserMetaFormTest extends TestCase
{
    /**
     * @group forms
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testConstruct(): void
    {
        $this->initDb();
        $user_meta = UserMeta::findOne(['user_id' => 1]);
        $form = new UserMetaForm([], $user_meta);
        $this->assertInstanceOf(UserMetaForm::class, $form);
        $this->assertEquals($form->notification_email, $user_meta->notification_email);
        $this->assertEquals($form->full_name, $user_meta->full_name);
        $this->assertEquals($form->tz, $user_meta->tz);
        $this->assertEquals($form->location, $user_meta->location);
        $this->assertEquals($form->about, $user_meta->about);
        $this->assertEquals($form->custom_fields, $user_meta->custom_fields);
    }
}
