<?php
namespace sorokinmedia\user\tests\handlers\CompanyUser\actions;

use sorokinmedia\user\handlers\CompanyUser\CompanyUserHandler;
use sorokinmedia\user\tests\entities\CompanyUser\CompanyUser;
use sorokinmedia\user\tests\TestCase;

/**
 * Class DeleteCompanyUserTest
 * @package sorokinmedia\user\tests\handlers\CompanyUser\actions
 */
class DeleteCompanyUserTest extends TestCase
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
        $handler = new CompanyUserHandler($company_user);
        $this->assertTrue($handler->delete());
    }
}