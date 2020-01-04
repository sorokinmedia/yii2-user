<?php

namespace sorokinmedia\user\tests\handlers\User\actions;

use sorokinmedia\user\forms\SignupForm;
use sorokinmedia\user\handlers\User\UserHandler;
use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\web\ServerErrorHttpException;

/**
 * Class CreateUserTest
 * @package sorokinmedia\user\tests\handlers\User\actions
 *
 * тестирование action create
 */
class CreateUserTest extends TestCase
{
    /**
     * @group user-handler
     * @throws InvalidConfigException
     * @throws Exception
     * @throws ServerErrorHttpException
     */
    public function testHandler(): void
    {
        $this->initDb();
        $user = new User();
        $signip_form = new SignupForm([
            'email' => 'Ma3oBblu@gmail.com',
            'username' => 'Ma3oBblu',
            'password' => 'test_password',
        ], $user, User::ROLE_ADMIN);
        $handler = new UserHandler($user);
        $this->assertTrue($handler->create($signip_form));
    }
}
