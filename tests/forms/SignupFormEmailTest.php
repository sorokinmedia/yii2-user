<?php

namespace sorokinmedia\user\tests\forms;

use sorokinmedia\user\forms\SignUpFormEmail;
use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\web\ServerErrorHttpException;

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
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testConstruct(): void
    {
        $this->initDb();
        $user = User::findOne(1);
        $form = new SignUpFormEmail([
            'email' => 'test@yandex.ru',
        ], $user, User::ROLE_WORKER);
        $this->assertInstanceOf(SignUpFormEmail::class, $form);
        $this->assertEquals($form->email, 'test@yandex.ru');
    }

    /**
     * @group forms
     * @group forms-signup_form_email
     * @throws \yii\base\Exception
     * @throws InvalidConfigException
     * @throws Exception
     * @throws ServerErrorHttpException
     */
    public function testSignUpFalse(): void
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
     * @throws InvalidConfigException
     * @throws Exception
     * @throws ServerErrorHttpException
     */
    public function testSignUpTrue(): void
    {
        $this->initDb();
        $user = new User();
        $form = new SignUpFormEmail([
            'email' => 'vasya@yandex.ru',
        ], $user, User::ROLE_WORKER);
        $this->assertTrue($form->signUp());
    }
}
