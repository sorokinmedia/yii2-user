<?php
namespace sorokinmedia\user\tests\handlers\CompanyUser\actions;

use sorokinmedia\user\forms\CompanyUserForm;
use sorokinmedia\user\handlers\CompanyUser\CompanyUserHandler;
use sorokinmedia\user\tests\entities\CompanyUser\CompanyUser;
use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;

/**
 * Class CreateSmsCodeTest
 * @package sorokinmedia\user\tests\handlers\CompanyUser\actions
 */
class CreateSmsCodeTest extends TestCase
{
    /**
     * @group company-user-handler
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testHandler()
    {
        $this->initDb();
        $this->initDbAdditional();
        $company_user = new CompanyUser();
        $company_user_form = new CompanyUserForm([
            'user_id' => 2,
            'company_id' => 1,
            'role' => User::ROLE_WORKER,
        ], $company_user);
        $company_user->form = $company_user_form;
        $handler = new CompanyUserHandler($company_user);
        $this->assertTrue($handler->create());
    }
}