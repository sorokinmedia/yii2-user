<?php
namespace sorokinmedia\user\tests\forms;

use sorokinmedia\user\entities\UserMeta\json\UserMetaPhone;
use sorokinmedia\user\forms\UserMetaForm;
use sorokinmedia\user\forms\UserMetaPhoneForm;
use sorokinmedia\user\tests\entities\UserMeta\UserMeta;
use sorokinmedia\user\tests\TestCase;
use yii\helpers\Json;

/**
 * Class UserMetaPhoneFormTest
 * @package sorokinmedia\user\tests\forms
 */
class UserMetaPhoneFormTest extends TestCase
{
    /**
     * @group forms
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function testConstruct()
    {
        $this->initDb();
        $user_meta = UserMeta::findOne(['user_id' => 1]);
        /** @var UserMetaPhone $phone */
        $phone = new UserMetaPhone(Json::decode($user_meta->notification_phone));
        $form = new UserMetaPhoneForm([], $phone);
        $this->assertInstanceOf(UserMetaPhoneForm::class, $form);
        $this->assertEquals($form->country, $phone->country);
        $this->assertEquals($form->number, $phone->number);
        $this->assertEquals($form->is_verified, $phone->is_verified);
    }
}