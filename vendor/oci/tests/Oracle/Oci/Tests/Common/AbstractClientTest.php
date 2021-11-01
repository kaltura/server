<?php

use Oracle\Oci\Common\AbstractClient;
use Oracle\Oci\Common\Auth\AuthProviderInterface;
use Oracle\Oci\Common\Auth\RegionProviderInterface;
use Oracle\Oci\Common\Logging\Logger;
use PHPUnit\Framework\TestCase;
use Oracle\Oci\Common\Realm;
use Oracle\Oci\Common\Region;

class AbstractClientTest extends TestCase
{
    private static $logger;

    /**
     * @beforeClass
     */
    public static function beforeClass()
    {
        // Logger::setGlobalLogAdapter(new EchoLogAdapter(LOG_DEBUG));
        AbstractClientTest::$logger = Logger::logger(static::class);
    }

    public function testDetermineRegion_WithRegion()
    {
        $auth_provider = new DummyAuthProvider();
        $region = Region::US_PHOENIX_1();
        $resultRegion = AbstractClient::determineRegion($region, $auth_provider, AbstractClientTest::$logger);
        $this->assertEquals($region, $resultRegion);
    }

    public function testDetermineRegion_WithRegion_WithRegionProvider()
    {
        $auth_provider = new DummyRegionAuthProvider(Region::US_PHOENIX_1());
        $region = Region::getRegion("us-ashburn-1");
        $resultRegion = AbstractClient::determineRegion($region, $auth_provider, AbstractClientTest::$logger);
        $this->assertEquals($region, $resultRegion);
    }

    public function testDetermineRegion_WithRegionProvider()
    {
        $auth_provider = new DummyRegionAuthProvider(Region::US_PHOENIX_1());
        $region = null;
        $resultRegion = AbstractClient::determineRegion($region, $auth_provider, AbstractClientTest::$logger);
        $this->assertEquals(Region::US_PHOENIX_1(), $resultRegion);
    }

    public function testDetermineRegion_WithRegionProvider_UnknownRegion()
    {
        $auth_provider = new DummyRegionAuthProvider(RegionsTest::getFreshUnknownRegionName());
        $region = null;
        try {
            AbstractClient::determineRegion($region, $auth_provider, AbstractClientTest::$logger);
            $this->fail("Should have thrown an InvalidArgumentException");
        } catch (InvalidArgumentException $iae) {
            // expected
        }
    }

    public function testDetermineRegion_WithRegionProvider_BadType()
    {
        $auth_provider = new DummyRegionAuthProvider(new DummyAuthProvider());
        $region = null;
        try {
            AbstractClient::determineRegion($region, $auth_provider, AbstractClientTest::$logger);
            $this->fail("Should have thrown an InvalidArgumentException");
        } catch (InvalidArgumentException $iae) {
            // expected
        }
    }

    public function testDetermineRegion_WithRegion_UnknownRegion()
    {
        $auth_provider = new DummyAuthProvider();
        $region = RegionsTest::getFreshUnknownRegionName();
        $resultRegion = AbstractClient::determineRegion($region, $auth_provider, AbstractClientTest::$logger);
        $this->assertTrue($resultRegion instanceof Region);
        $this->assertEquals($region, $resultRegion->getRegionId());
        $this->assertEquals($region, $resultRegion->getRegionCode());
        $this->assertEquals(Realm::getRealmForUnknownRegion(), $resultRegion->getRealm());
    }

    public function testDetermineEndpoint_WithEndpoint()
    {
        $endpoint = "a.b.com";
        $region = null;
        $endpointTemplate = null;
        $resultEndpoint = AbstractClient::determineEndpoint($endpoint, $region, $endpointTemplate, AbstractClientTest::$logger);

        $this->assertEquals($endpoint, $resultEndpoint);
    }

    public function testDetermineEndpoint_WithEndpoint_WithRegion()
    {
        $endpoint = "a.b.com";
        $region = Region::US_PHOENIX_1();
        $endpointTemplate = null;
        $resultEndpoint = AbstractClient::determineEndpoint($endpoint, $region, $endpointTemplate, AbstractClientTest::$logger);

        $this->assertEquals($endpoint, $resultEndpoint);
    }

    public function testDetermineEndpoint_WithRegion()
    {
        $endpoint = null;
        $region = Region::US_PHOENIX_1();
        $endpointTemplate = "https://objectstorage.{region}.{secondLevelDomain}";
        $resultEndpoint = AbstractClient::determineEndpoint($endpoint, $region, $endpointTemplate, AbstractClientTest::$logger);

        $this->assertEquals("https://objectstorage.{$region->getRegionId()}.{$region->getRealm()->getRealmDomainComponent()}", $resultEndpoint);
    }

    public function testDetermineEndpoint_WithRegion_BadType()
    {
        $endpoint = null;
        $region = "us-phoenix-1";
        $endpointTemplate = "https://objectstorage.{region}.{secondLevelDomain}";
        try {
            AbstractClient::determineEndpoint($endpoint, $region, $endpointTemplate, AbstractClientTest::$logger);
            $this->fail("Should have thrown an InvalidArgumentException");
        } catch (InvalidArgumentException $iae) {
            // expected
        }
    }
}

class DummyAuthProvider implements AuthProviderInterface
{
    public function getPrivateKey() //  : string
    {
        return null;
    }
    public function getKeyPassphrase() // : ?string
    {
        return null;
    }
    public function getKeyId() // : string
    {
        return null;
    }
}

class DummyRegionAuthProvider extends DummyAuthProvider implements RegionProviderInterface
{
    private $region;

    public function __construct($region)
    {
        $this->region = $region;
    }

    public function getRegion() // : Region
    {
        return $this->region;
    }
}
