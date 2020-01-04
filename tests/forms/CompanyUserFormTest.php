<?php

namespace sorokinmedia\user\tests\forms;

use sorokinmedia\user\forms\CompanyUserForm;
use sorokinmedia\user\tests\entities\CompanyUser\CompanyUser;
use sorokinmedia\user\tests\TestCase;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\web\ServerErrorHttpException;

/**
 * Class CompanyUserFormTest
 * @package sorokinmedia\user\tests\forms
 */
class CompanyUserFormTest extends TestCase
{
    /**
     * @group forms
     * @throws InvalidConfigException
     * @throws Exception
     * @throws ServerErrorHttpException
     */
    public function testConstruct(): void
    {
        $this->initDb();
        $company_user = CompanyUser::findOne(['user_id' => 1, 'company_id' => 1]);
        $form = new CompanyUserForm([], $company_user);
        $this->assertInstanceOf(CompanyUserForm::class, $form);
        $this->assertEquals($form->user_id, $company_user->user_id);
        $this->assertEquals($form->company_id, $company_user->company_id);
        $this->assertEquals($form->role, $company_user->role);
    }
}
