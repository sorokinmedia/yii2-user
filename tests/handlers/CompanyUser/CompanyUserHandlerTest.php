<?php
namespace sorokinmedia\user\tests\handlers\CompanyUser;

use sorokinmedia\user\handlers\CompanyUser\CompanyUserHandler;
use sorokinmedia\user\tests\entities\CompanyUser\CompanyUser;
use sorokinmedia\user\tests\TestCase;

/**
 * Class CompanyUserHandlerTest
 * @package sorokinmedia\user\tests\handlers\SmsCode
 *
 * тестирование хендлера CompanyUser
 */
class CompanyUserHandlerTest extends TestCase
{
    /**
     * @group company-user-handler
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\web\ServerErrorHttpException
     */
    public function testHandler()
    {
        $this->initDb();
        $company_user = CompanyUser::findOne(['user_id' => 1, 'company_id' => 1]);
        $handler = new CompanyUserHandler($company_user);
        $this->assertInstanceOf(CompanyUserHandler::class, $handler);
        $this->assertInstanceOf(CompanyUser::class, $handler->company_user);
    }
}