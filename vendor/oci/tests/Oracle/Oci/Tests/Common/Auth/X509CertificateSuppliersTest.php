<?php

namespace Oracle\Oci\Common\Auth;

use PHPUnit\Framework\TestCase;

class X509CertificateSuppliersTest extends TestCase
{
    /**
     * @group InstancePrincipalsRequired
     */
    public function testUrlBasedX509CertificateSupplier()
    {
        $cs = new UrlBasedX509CertificateSupplier(
            X509FederationClient::METADATA_SERVICE_BASE_URL . "identity/cert.pem",
            X509FederationClient::METADATA_SERVICE_BASE_URL . "identity/key.pem",
            null
        );
        $kp = $cs->getKeyPair();

        $this->assertTrue(strpos($kp->getPublicKey(), "BEGIN CERTIFICATE") != false);
        $this->assertTrue(strpos($kp->getPrivateKey(), "BEGIN RSA PRIVATE KEY") != false);
    }
}
