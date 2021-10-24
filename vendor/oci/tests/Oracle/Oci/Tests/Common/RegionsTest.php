<?php

use PHPUnit\Framework\TestCase;
use Oracle\Oci\Common\Realm;
use Oracle\Oci\Common\Region;

class RegionsTest extends TestCase
{
    public function testRealms()
    {
        $this->assertEquals("oc1", Realm::getRealm("oc1")->getRealmId());
        $this->assertEquals("oc1", Realm::getRealm("OC1")->getRealmId());
        $this->assertEquals(Realm::getRealm("oc1"), Realm::getRealm("oC1"));
        $this->assertEquals("oraclecloud.com", Realm::getRealm("oc1")->getRealmDomainComponent());
        $this->assertEquals("oc2", Realm::getRealm("oc2")->getRealmId());
        $this->assertEquals("oc3", Realm::getRealm("oc3")->getRealmId());
        $this->assertEquals("oc4", Realm::getRealm("oc4")->getRealmId());
        $this->assertEquals("oc8", Realm::getRealm("oc8")->getRealmId());
    }

    public function testUnknownRealm()
    {
        $this->assertEquals(null, Realm::getRealm("oc17"));
        $this->assertEquals(Realm::getRealm("oc1"), Realm::getRealmForUnknownRegion());
    }

    public function testRegions()
    {
        $this->assertEquals("us-phoenix-1", Region::getRegionById("us-phoenix-1")->getRegionId());
        $this->assertEquals("us-phoenix-1", Region::getRegionById("us-PhOenIx-1")->getRegionId());
        $this->assertEquals("phx", Region::getRegionById("us-phoenix-1")->getRegionCode());
        $this->assertEquals("oc1", Region::getRegionById("us-phoenix-1")->getRealm()->getRealmId());
        $this->assertEquals(Region::getRegionByCode("phx"), Region::getRegionById("us-phoenix-1"));
        $this->assertEquals(Region::getRegionByCode("PhX"), Region::getRegionById("us-phoenix-1"));
        $this->assertEquals(Region::getRegion("phx"), Region::getRegionById("us-phoenix-1"));
        $this->assertEquals(Region::getRegion("PhX"), Region::getRegionById("us-phoenix-1"));
        $this->assertEquals(Region::getRegion("us-phoenix-1"), Region::getRegionById("us-phoenix-1"));
        $this->assertEquals(Region::getRegion("US-phoeNIX-1"), Region::getRegionById("us-phoenix-1"));

        $this->assertEquals("oc2", Region::getRegionById("us-langley-1")->getRealm()->getRealmId());
        $this->assertEquals("oc3", Region::getRegionById("us-gov-ashburn-1")->getRealm()->getRealmId());
        $this->assertEquals("oc4", Region::getRegionById("uk-gov-london-1")->getRealm()->getRealmId());
        $this->assertEquals("oc8", Region::getRegionById("ap-chiyoda-1")->getRealm()->getRealmId());
    }

    public function testUnknownRegion()
    {
        $this->assertEquals(null, Region::getRegion("sp-mars-1"));
    }
}
