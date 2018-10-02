<?php
namespace sorokinmedia\user\tests\forms;

use sorokinmedia\user\entities\User\UserInterface;
use sorokinmedia\user\forms\SignUpFormEmail;
use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;

/**
 * Class SignupFormEmailTest
 * @package sorokinmedia\user\tests\forms
 *
 * тесты формы для регистрации по email
 */
class SignupFormEmailTest extends TestCase
{
    /**
     * @group forms
     * @group forms-signup_form_email
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function testConstruct()
    {
        $this->initDb();
        $user = User::findOne(1);
        $form = new SignUpFormEmail([
            'email' => 'test@yandex.ru',
        ], $user,  User::ROLE_WORKER);
        $this->assertInstanceOf(SignUpFormEmail::class, $form);
        $this->assertEquals($form->email, 'test@yandex.ru');
        $this->assertInstanceOf(UserInterface::class, $form->getUser());
    }

    /**
     * @group forms
     * @group forms-signup_form_email
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function testSignUpFalse()
    {
        $this->initDb();
        $user = new User();
        $form = new SignUpFormEmail([
            'email' => 'test@yandex.ru',
        ], $user, User::ROLE_WORKER);
        $this->assertFalse($form->signUp());
        $this->assertNotNull($form->errors);
        $this->assertEquals($form->errors['email'][0], 'Этот E-mail уже зарегистрирован в системе. Попробуйте использовать другой или восстановить пароль, указав текущий.');
    }

    /**
     * @group forms
     * @group forms-signup_form_email
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function testSignUpTrue()
    {
        $this->initDb();
        $user = new User();
        $form = new SignUpFormEmail([
            'email' => 'vasya@yandex.ru',
        ], $user, User::ROLE_WORKER);
        $this->assertTrue($form->signUp());
    }
}