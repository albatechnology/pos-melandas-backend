<?php

namespace Tests\Unit\API\Doc;

use App\Models\Company;

/**
 * Class CompanyTest
 * @package Tests\Unit\API
 */
class CompanyDocTest extends BaseApiDocTest
{
    protected Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::first() ?? Company::factory()->create();
    }

    /**
     * @group Doc
     * @return void
     */
    public function testCompanyIndex()
    {
        $this->makeApiTest(route('companies.index', [], false), 'get');
    }

    /**
     * @group Doc
     * @return void
     */
    public function testCompanyShow()
    {
        $this->makeApiTest(route('companies.show', [$this->company->id], false), 'get', null, 0);
    }
}
