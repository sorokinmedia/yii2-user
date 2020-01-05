<?php

namespace sorokinmedia\user\tests\forms;

use sorokinmedia\user\forms\SignUpFormEmail;
use sorokinmedia\user\forms\SignUpFormExisted;
use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\web\ServerErrorHttpException;

/**
 * Class SignupFormEmailTest
 * @package sorokinmedia\user\tests\forms
 *
 * тесты формы для регистрации с перенос пользователя
 */
class SignUpFormExistedTest extends TestCase
{
    /**
     * @group forms
     * @group forms-signup_form_existed
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testConstruct(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $form = new SignUpFormExisted([
            'username' => 'maza-87',
            'email' => 'maza-87@mail.ru',
            'password' => '$2y$13$J/R9DEe0Q/L7fWgPwMo7Hunca2NByL4xbTS3gd44/lqBeGNu8Dzwm' // another_test_password
        ], $user, User::ROLE_WORKER);
        $this->assertInstanceOf(SignUpFormExisted::class, $form);
        $this->assertEquals($form->email, 'maza-87@mail.ru');
        $this->assertEquals($form->password, '$2y$13$J/R9DEe0Q/L7fWgPwMo7Hunca2NByL4xbTS3gd44/lqBeGNu8Dzwm'); // another_test_password
    }

    /**
     * @group forms
     * @group forms-signup_form_existed
     * @throws \yii\base\Exception
     * @throws InvalidConfigException
     * @throws Exception
     * @throws ServerErrorHttpException
     */
    public function testSignUpFalse(): void
    {
        $this->initDb();
        $user = new User();
        $form = new SignUpFormExisted([
            'username' => 'maza-87',
            'email' => 'test@yandex.ru',
            'password' => '$2y$13$J/R9DEe0Q/L7fWgPwMo7Hunca2NByL4xbTS3gd44/lqBeGNu8Dzwm' // another_test_password
        ], $user, User::ROLE_WORKER);
        $this->assertFalse($form->signUp());
        $this->assertNotNull($form->errors);
        $this->assertEquals($form->errors['email'][0], 'Этот E-mail уже зарегистрирован в системе. Попробуйте использовать другой или восстановить пароль, указав текущий.');
    }

    /**
     * @group forms
     * @group forms-signup_form_existed
     * @throws \yii\base\Exception
     * @throws InvalidConfigException
     * @throws Exception
     * @throws ServerErrorHttpException
     */
    public function testSignUpTrue(): void
    {
        $this->initDb();
        $user = new User();
        $form = new SignUpFormExisted([
            'username' => 'maza-87',
            'email' => 'maza-87@mail.ru',
            'password' => '$2y$13$J/R9DEe0Q/L7fWgPwMo7Hunca2NByL4xbTS3gd44/lqBeGNu8Dzwm' // another_test_password
        ], $user, User::ROLE_WORKER);
        $this->assertTrue($form->signUp());
        $this->assertTrue($user->validatePassword('another_test_password'));
    }
}
