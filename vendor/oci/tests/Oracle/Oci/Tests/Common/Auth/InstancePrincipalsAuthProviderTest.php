<?php

namespace Oracle\Oci\Common\Auth;

use Oracle\Oci\Common\Region;
use Oracle\Oci\ObjectStorage\ObjectStorageClient;
use PHPUnit\Framework\TestCase;

class InstancePrincipalsAuthProviderTest extends TestCase
{
    /**
     * @beforeClass
     */
    public static function beforeClass()
    {
        \Oracle\Oci\Common\Logging\Logger::setGlobalLogAdapter(new \Oracle\Oci\Common\Logging\EchoLogAdapter(LOG_INFO, [
            "Oracle\Oci\Common\Auth" => LOG_DEBUG,
            "Oracle\Oci\ObjectStorage" => LOG_DEBUG
        ]));
    }

    public function testSessionKeySupplierImpl()
    {
        $sks = new SessionKeySupplierImpl();

        $kp = $sks->getKeyPair();
        $this->assertTrue(strpos($kp->getPublicKey(), "BEGIN PUBLIC KEY") != false);
        $this->assertTrue(strpos($kp->getPrivateKey(), "BEGIN PRIVATE KEY") != false);

        $sks->refreshKeys();

        $kp2 = $sks->getKeyPair();
        $this->assertContains("BEGIN PUBLIC KEY", $kp2->getPublicKey());
        $this->assertContains("BEGIN PRIVATE KEY", $kp2->getPrivateKey());

        $this->assertNotEquals($kp->getPublicKey(), $kp2->getPublicKey());
        $this->assertNotEquals($kp->getPrivateKey(), $kp2->getPrivateKey());

        $kp3 = $sks->getKeyPair();
        $this->assertContains("BEGIN PUBLIC KEY", $kp3->getPublicKey());
        $this->assertContains("BEGIN PRIVATE KEY", $kp3->getPrivateKey());

        $this->assertEquals($kp3->getPublicKey(), $kp2->getPublicKey());
        $this->assertEquals($kp3->getPrivateKey(), $kp2->getPrivateKey());
    }

    /**
     * @group InstancePrincipalsRequired
     */
    public function testInstancePrincipalsAuthProvider_Simplest()
    {
        $ipap = new InstancePrincipalsAuthProvider();
        
        $keyId = $ipap->getKeyId();
        $this->assertTrue(strpos($keyId, "ST$") == 0);
    }

    /**
     * @group InstancePrincipalsRequired
     */
    public function testInstancePrincipalsAuthProvider_AutoDetectEverything()
    {
        $sessionKeySupplier = new SessionKeySupplierImpl();
        $fc = new X509FederationClient($sessionKeySupplier);
        $ipap = new InstancePrincipalsAuthProvider([
            InstancePrincipalsAuthProvider::FEDERATION_CLIENT => $fc,
            InstancePrincipalsAuthProvider::SESSION_KEY_SUPPLIER => $sessionKeySupplier,
        ]);
        
        $keyId = $ipap->getKeyId();
        $this->assertTrue(strpos($keyId, "ST$") == 0);
    }

    /**
     * @group InstancePrincipalsRequired
     */
    public function testInstancePrincipalsAuthProvider_RegionSet()
    {
        $sessionKeySupplier = new SessionKeySupplierImpl();
        $fc = new X509FederationClient($sessionKeySupplier, [
            'region' => Region::US_PHOENIX_1()
        ]);
        $ipap = new InstancePrincipalsAuthProvider([
            InstancePrincipalsAuthProvider::FEDERATION_CLIENT => $fc,
            InstancePrincipalsAuthProvider::SESSION_KEY_SUPPLIER => $sessionKeySupplier,
            InstancePrincipalsAuthProvider::REGION => Region::US_PHOENIX_1()
        ]);
        
        $keyId = $ipap->getKeyId();
        $this->assertTrue(strpos($keyId, "ST$") == 0);
    }

    /**
     * @group InstancePrincipalsRequired
     */
    public function testObjectStorageWithInstancePrincipalsAuthProvider()
    {
        $sessionKeySupplier = new SessionKeySupplierImpl();
        $fc = new X509FederationClient($sessionKeySupplier);
        $ipap = new InstancePrincipalsAuthProvider([
            InstancePrincipalsAuthProvider::FEDERATION_CLIENT => $fc,
            InstancePrincipalsAuthProvider::SESSION_KEY_SUPPLIER => $sessionKeySupplier,
        ]);

        $c = new ObjectStorageClient($ipap);
        
        $response = $c->getNamespace();
        $namespace = $response->getJson();
        
        $this->assertTrue(strlen($namespace) > 0);
    }

    /**
     * @group InstancePrincipalsRequired
     */
    public function testObjectStorageWithInstancePrincipalsAuthProvider_Simplest()
    {
        $ipap = new InstancePrincipalsAuthProvider();

        $c = new ObjectStorageClient($ipap);
        
        $response = $c->getNamespace();
        $namespace = $response->getJson();
        
        $this->assertTrue(strlen($namespace) > 0);
    }
}
