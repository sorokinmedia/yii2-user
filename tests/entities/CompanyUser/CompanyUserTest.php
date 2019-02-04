<?php
namespace sorokinmedia\user\tests\entities\CompanyUser;

use sorokinmedia\user\forms\CompanyUserForm;
use sorokinmedia\user\tests\entities\Company\Company;
use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;
use yii\db\Exception;

/**
 * Class CompanyUserTest
 * @package sorokinmedia\user\tests\entities\CompanyUser
 */
class CompanyUserTest extends TestCase
{
    /**
     * @group company-user
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testFields()
    {
        $this->initDb();
        $company_user = new CompanyUser();
        $this->assertEquals(
            [
                'company_id',
                'user_id',
                'role',
            ],
            array_keys($company_user->getAttributes())
        );
    }

    /**
     * @group company-user
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testRelations()
    {
        $this->initDb();
        $company_user = CompanyUser::findOne(['user_id' => 1, 'company_id' => 1]);
        $this->assertInstanceOf(CompanyUser::class, $company_user);
        $this->assertInstanceOf(User::class, $company_user->getUser()->one());
        $this->assertInstanceOf(Company::class, $company_user->getCompany()->one());
        //need test getRoleObject()
    }

    /**
     * @group company-user
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testGetFromForm()
    {
        $this->initDb();
        $form = new CompanyUserForm([
            'user_id' => 2,
            'company_id' => 2,
            'role' => User::ROLE_WORKER
        ]);
        $company_user = new CompanyUser();
        $company_user->form = $form;
        $this->assertInstanceOf(CompanyUser::class, $company_user);
        $this->assertInstanceOf(CompanyUserForm::class, $company_user->form);
        $company_user->getFromForm();
        $this->assertEquals($form->user_id, $company_user->user_id);
        $this->assertEquals($form->company_id, $company_user->company_id);
        $this->assertEquals($form->role, $company_user->role);
    }

    /**
     * @group company-user
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testInsertModel()
    {
        $this->initDb();
        $this->initDbAdditional();
        $user = User::findOne(2);
        $form = new CompanyUserForm([
            'user_id' => $user->id,
            'company_id' => 1,
            'role' => User::ROLE_WORKER
        ]);
        $company_user = new CompanyUser();
        $company_user->form = $form;
        $this->assertTrue($company_user->insertModel());
        $company_user->refresh();
        $this->assertInstanceOf(CompanyUser::class, $company_user);
        $this->assertEquals($form->user_id, $company_user->user_id);
        $this->assertEquals($form->company_id, $company_user->company_id);
        $this->assertEquals($form->role, $company_user->role);
    }

    /**
     * @group company-user
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testUpdateModel()
    {
        $this->initDb();
        $company_user = CompanyUser::findOne(['user_id' => 1, 'company_id' => 1]);
        $form = new CompanyUserForm([
            'role' => User::ROLE_ADMIN
        ], $company_user);
        $company_user->form = $form;
        $this->assertTrue($company_user->updateModel());
        $company_user->refresh();
        $this->assertEquals($form->role, $company_user->role);
    }

    /**
     * @group company-user
     * @throws \Throwable
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function testDeleteModel()
    {
        $this->initDb();
        $company_user = CompanyUser::findOne(['user_id' => 1, 'company_id' => 1]);
        $this->assertTrue($company_user->deleteModel());
        $deleted_company_user = CompanyUser::findOne(['user_id' => 1, 'company_id' => 1]);
        $this->assertNull($deleted_company_user);
    }
}