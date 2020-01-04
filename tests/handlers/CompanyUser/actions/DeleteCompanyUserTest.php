<?php

namespace sorokinmedia\user\tests\handlers\CompanyUser\actions;

use sorokinmedia\user\handlers\CompanyUser\CompanyUserHandler;
use sorokinmedia\user\tests\entities\CompanyUser\CompanyUser;
use sorokinmedia\user\tests\TestCase;
use Throwable;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 * Class DeleteCompanyUserTest
 * @package sorokinmedia\user\tests\handlers\CompanyUser\actions
 */
class DeleteCompanyUserTest extends TestCase
{
    /**
     * @group company-user-handler
     * @throws Throwable
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testHandler(): void
    {
        $this->initDb();
        $this->initDbAdditional();
        $company_user = CompanyUser::findOne(['user_id' => 1, 'company_id' => 1]);
        $handler = new CompanyUserHandler($company_user);
        $this->assertTrue($handler->delete());
    }
}
