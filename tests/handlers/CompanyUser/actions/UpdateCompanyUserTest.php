<?php
namespace sorokinmedia\user\tests\handlers\CompanyUser\actions;

use sorokinmedia\user\forms\CompanyUserForm;
use sorokinmedia\user\handlers\CompanyUser\CompanyUserHandler;
use sorokinmedia\user\tests\entities\CompanyUser\CompanyUser;
use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;

/**
 * Class UpdateCompanyUserTest
 * @package sorokinmedia\user\tests\handlers\CompanyUser\actions
 */
class UpdateCompanyUserTest extends TestCase
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
        $company_user = CompanyUser::findOne(['user_id' => 1, 'company_id' => 1]);
        $company_user_form = new CompanyUserForm([
            'role' => User::ROLE_OWNER,
        ], $company_user);
        $company_user->form = $company_user_form;
        $handler = new CompanyUserHandler($company_user);
        $this->assertTrue($handler->update());
        $company_user->refresh();
        $this->assertEquals($company_user_form->role, $company_user->role);
    }
}