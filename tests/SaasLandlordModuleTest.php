<?php

namespace Rhubarb\Scaffolds\Saas\Landlord\Tests;

use Rhubarb\Scaffolds\Saas\Landlord\SaasLandlordModule;
use Rhubarb\Scaffolds\Saas\Landlord\Tests\Fixtures\SaasTestCase;

class SaasLandlordModuleTest extends SaasTestCase
{
    public function testCanRegisterAppServerIP()
    {
        SaasLandlordModule::clearRegisteredTenantServers();
        SaasLandlordModule::registerTenantServer('192.168.1.1');
        $this->assertCount(1, SaasLandlordModule::getTenantServerIPAddresses());
    }

    public function testCanRegisterAppServerMask()
    {
        SaasLandlordModule::clearRegisteredTenantServers();
        SaasLandlordModule::registerTenantServer('192.168.1.0/24');
        $this->assertCount(1, SaasLandlordModule::getTenantServerMasks());
    }

    public function testCanClearRegisteredAppServers()
    {
        SaasLandlordModule::clearRegisteredTenantServers();
        SaasLandlordModule::registerTenantServer('192.168.1.1');
        SaasLandlordModule::registerTenantServer('192.168.1.0/24');
        SaasLandlordModule::clearRegisteredTenantServers();
        $this->assertCount(0, SaasLandlordModule::getTenantServerIPAddresses());
        $this->assertCount(0, SaasLandlordModule::getTenantServerMasks());
    }

    public function testIsAppServerByIP()
    {
        SaasLandlordModule::clearRegisteredTenantServers();
        SaasLandlordModule::registerTenantServer('192.168.1.1');
        $this->assertTrue(SaasLandlordModule::isTenantServer('192.168.1.1'));
        $this->assertFalse(SaasLandlordModule::isTenantServer('192.168.2.1'));
    }

    public function testIsAppServerByMask()
    {
        SaasLandlordModule::clearRegisteredTenantServers();
        SaasLandlordModule::registerTenantServer('192.168.1.0/24');
        $this->assertTrue(SaasLandlordModule::isTenantServer('192.168.1.1'));
        $this->assertFalse(SaasLandlordModule::isTenantServer('192.168.2.1'));
    }
}