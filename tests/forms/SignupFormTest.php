<?php
namespace sorokinmedia\user\tests\forms;

use sorokinmedia\user\entities\User\UserInterface;
use sorokinmedia\user\forms\SignupForm;
use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;

/**
 * Class PasswordChangeFormTest
 * @package sorokinmedia\user\tests\forms
 *
 * тест формы смены пароля
 */
class SignupFormTest extends TestCase
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
        $user = User::findOne(1);
        $form = new SignupForm([
            'username' => 'test',
            'email' => 'test@yandex.ru',
            'password' => 'test_password'
        ], $user);
        $this->assertInstanceOf(SignupForm::class, $form);
        $this->assertEquals($form->username, 'test');
        $this->assertEquals($form->email, 'test@yandex.ru');
        $this->assertEquals($form->password, 'test_password');
        $this->assertInstanceOf(UserInterface::class, $form->getUser());
    }

    /**
     * @group forms
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function testSignUpFalse()
    {
        $this->initDb();
        $user = new User();
        $form = new SignupForm([
            'username' => 'IvanSidorov',
            'email' => 'test@yandex.ru',
            'password' => 'test_password'
        ], $user);
        $this->assertFalse($form->signUp());
        $this->assertNotNull($form->errors);
        $this->assertEquals($form->errors['email'][0], 'Этот E-mail уже зарегистрирован в системе. Попробуйте использовать другой или восстановить пароль, указав текущий.');
        $form->email = 'test1@yandex.ru';
        $this->assertFalse($form->signUp());
        $this->assertNotNull($form->errors);
        $this->assertEquals($form->errors['username'][0], 'Этот логин уже занят. Попробуйте использовать другой.');
    }

    /**
     * @group forms
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function testSignUpTrue()
    {
        $this->initDb();
        $user = new User();
        $form = new SignupForm([
            'username' => 'VasyaPupkin',
            'email' => 'vasya@yandex.ru',
            'password' => 'test_password'
        ], $user);
        $this->assertTrue($form->signUp());
    }
}