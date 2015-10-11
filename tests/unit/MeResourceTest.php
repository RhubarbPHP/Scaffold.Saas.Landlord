<?php


use Rhubarb\Scaffolds\Saas\Landlord\Tests\Fixtures\SaasApiTestCase;

class MeResourceTest extends SaasApiTestCase
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testUserIdentifiedByUUID()
    {
        $response = $this->makeApiCall( "/users/me" );

        $this->assertGreaterThan(24, strlen( $response->_id) );
    }

    public function testUUIDInResponse()
    {
        $response = $this->makeApiCall( "/users/me" );

        $this->assertNotEmpty($response->UUID);
    }
}