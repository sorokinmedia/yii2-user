<?php

namespace sorokinmedia\user\tests\entities\Company;

use sorokinmedia\user\tests\entities\User\User;
use sorokinmedia\user\tests\TestCase;
use Throwable;
use yii\base\InvalidConfigException;
use yii\db\Exception;

/**
 * Class CompanyTest
 * @package sorokinmedia\user\tests\entities\Company
 */
class CompanyTest extends TestCase
{
    /**
     * @group company
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testFields(): void
    {
        $this->initDb();
        $company = new Company();
        $this->assertEquals(
            [
                'id',
                'owner_id',
                'name',
                'description',
            ],
            array_keys($company->getAttributes())
        );
    }

    /**
     * @group company
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testRelations(): void
    {
        $this->initDb();
        $company = Company::findOne(1);
        $this->assertInstanceOf(Company::class, $company);
        $this->assertInstanceOf(User::class, $company->getOwner()->one());
        $this->assertNotEmpty($company->getUsers()->all());
    }

    /**
     * @group company
     * @throws Throwable
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function testCreate(): void
    {
        $this->initDb();
        $this->initDbAdditional();
        $user = User::findOne(2);
        /** @var Company $new_company */
        $new_company = Company::create($user, User::ROLE_OWNER);
        $this->assertInstanceOf(Company::class, $new_company);
        $this->assertEquals($user->id, $new_company->owner_id);
        $this->assertEquals('Моя компания', $new_company->name);
        $this->assertNull($new_company->description);
    }
}
