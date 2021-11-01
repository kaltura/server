<?php

namespace Oracle\Oci\Common\Auth;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FederationClientTest extends TestCase
{
    private static $sessionKeyProvider;
    private static $cert;

    /**
     * @before
     */
    public function beforeClass()
    {
        FederationClientTest::$sessionKeyProvider = new SessionKeySupplierImpl();
        // Logger::setGlobalLogAdapter(new EchoLogAdapter(LOG_INFO, [
        //     "Oracle\Oci\Common\Auth" => LOG_DEBUG
        // ], []));

        FederationClientTest::$cert = "-----BEGIN CERTIFICATE-----" . PHP_EOL . PHP_EOL .
        "MIIH9zCCBd+gAwIBAgIQMpG/01d3at9GHSHZA3/wmTANBgkqhkiG9w0BAQsFADCB" . PHP_EOL .
        "nzFzMHEGA1UECxNqb3BjLWRldmljZTo4NTplYzpiMDo5ZTozYTo2YzpjZTpmNDo1" . PHP_EOL .
        "Yjo3YTplZjo5ZDo5Yjo5Zjo3NDo2ODo5MDoyOTo2Zjo3Yjo1NTo0MjpmNzo4Zjo3" . PHP_EOL .
        "Zjo1MzpmYTpjNTo2MzoxNjoxNzo0ODEoMCYGA1UEAxMfUEtJU1ZDIElkZW50aXR5" . PHP_EOL .
        "IEludGVybWVkaWF0ZSByMjAeFw0yMTEwMjIyMzEzMzZaFw0yMTEwMjMwMTE0MzZa" . PHP_EOL .
        "MIIBvDFcMFoGA1UEAxNTb2NpZDEuaW5zdGFuY2Uub2MxLnBoeC5hbnlocWxqdGV2" . PHP_EOL .
        "NDNma2ljbHNhY2lwY2tlamYzeHZ6YnlycWxhdHdrcmNjYndyaXIyMnVwNXo2bXNn" . PHP_EOL .
        "b3ExHjAcBgNVBAsTFW9wYy1jZXJ0dHlwZTppbnN0YW5jZTFsMGoGA1UECxNjb3Bj" . PHP_EOL .
        "LWNvbXBhcnRtZW50Om9jaWQxLmNvbXBhcnRtZW50Lm9jMS4uYWFhYWFhYWFnYzZ4" . PHP_EOL .
        "dnl1aHBsdTNta2I0ZXdtZ2ptYTZ1dXhmd3o1NmQzZ2s2YWxwc2M1YmZqNTR3d25h" . PHP_EOL .
        "MWkwZwYDVQQLE2BvcGMtaW5zdGFuY2U6b2NpZDEuaW5zdGFuY2Uub2MxLnBoeC5h" . PHP_EOL .
        "bnlocWxqdGV2NDNma2ljbHNhY2lwY2tlamYzeHZ6YnlycWxhdHdrcmNjYndyaXIy" . PHP_EOL .
        "MnVwNXo2bXNnb3ExYzBhBgNVBAsTWm9wYy10ZW5hbnQ6b2NpZDEudGVuYW5jeS5v" . PHP_EOL .
        "YzEuLmFhYWFhYWFhY3FwNDMyaHBhNW9jMmt2eG00a3B3YmtvZGZydTRva2J3Mm9i" . PHP_EOL .
        "a2Nkb2I1enVlZ2k0cnd4cTCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEB" . PHP_EOL .
        "AN3nrnoZt9jnrkIPPm535NQrxEBThuyrKpaM8K5z2xQh0FVHNw4pABTXxveUG17s" . PHP_EOL .
        "LGpwqY/5ResJR6fX/x24RVmJpFY+zDkvqi02SuBnqet3gGh7wpKfTKOOjfdaSWBm" . PHP_EOL .
        "YkNNBCBeDed3wseSNisvifT0XdGzrv0EOKBIcmVOvZtm3Xf5YSzx1UFEvUSVBeuZ" . PHP_EOL .
        "e31g0f+mj6vSlPHybiTpY2Clbm+YBgYqFd6eZqW4Qjw8X0RQKEpwEG8pP4wkumQh" . PHP_EOL .
        "czefa0dZhK8eYWIFHu++GL9v7zxSJJfroAMqBfyRfR9JC5HEm57LD3pCLbJnaZtl" . PHP_EOL .
        "pUqzdncuuAJ0p+l9Ax9UAfkCAwEAAaOCAg0wggIJMBMGA1UdJQQMMAoGCCsGAQUF" . PHP_EOL .
        "BwMCMB8GA1UdIwQYMBaAFJEtLz030oPzx0Gkq26nMWnlxwlCMIIBzwYJKwYBBAFv" . PHP_EOL .
        "YgoBBIIBwDCCAbyBCGluc3RhbmNlglNvY2lkMS5pbnN0YW5jZS5vYzEucGh4LmFu" . PHP_EOL .
        "eWhxbGp0ZXY0M2ZraWNsc2FjaXBja2VqZjN4dnpieXJxbGF0d2tyY2Nid3JpcjIy" . PHP_EOL .
        "dXA1ejZtc2dvcYNTb2NpZDEuY29tcGFydG1lbnQub2MxLi5hYWFhYWFhYWdjNnh2" . PHP_EOL .
        "eXVocGx1M21rYjRld21nam1hNnV1eGZ3ejU2ZDNnazZhbHBzYzViZmo1NHd3bmGE" . PHP_EOL .
        "T29jaWQxLnRlbmFuY3kub2MxLi5hYWFhYWFhYWNxcDQzMmhwYTVvYzJrdnhtNGtw" . PHP_EOL .
        "d2Jrb2RmcnU0b2tidzJvYmtjZG9iNXp1ZWdpNHJ3eHGFgbRBUUVDQVIrTENBQUFB" . PHP_EOL .
        "QUFBQUFCanFsWUtTeTBxenN6UFU3SXkxRkh5TFBZcnpjbHhLMHBOVGNzdnlnMUpU" . PHP_EOL .
        "QzlXc2lvcEtrMkZ5YmlrcG1YbXBhWkFKTklTYzRxQk1pbklZdFcxT2tyQk9hWHBB" . PHP_EOL .
        "VVg1QmFsRkpabXBJREVsa0p4WGNYNWVjR1pWcW1lZVUyVUpTRmpKU01kSXFSWm1j" . PHP_EOL .
        "bkJsY1VrcWtvMjFBTmJOTjVDYUFBQUEwDQYJKoZIhvcNAQELBQADggIBADcGaVoi" . PHP_EOL .
        "DcXKPszeHqFnTXWeBc7wr2viLXsM9Duzjs+yK2RFuNS4GtClOLBjFWVlvvHJvSoM" . PHP_EOL .
        "iuqpglav9gCGrplDx1x8N7bq3+mCS47HFkTKlz2xFl+dkrgTpJSc9rcfBVb/Ls19" . PHP_EOL .
        "KiodYVTr9no935MYMQWvYnpwgpbnhSObLx/QlgnWvBwFhseGTnNJafblXxpUInYK" . PHP_EOL .
        "zkxgKeXGghTA2JFNPe+DqjY1LvcCRRkD+FpetOKkxGj6rnz6nxbZvzAJSNbwG5ju" . PHP_EOL .
        "17+C+ytgUlVJplLC+l7issGn29s5TQ/u21b/TwFxZV1r9lnOEiTgqoxA8tLrxS3o" . PHP_EOL .
        "uMrARwWXY+Pz3wByyalPqs2cyCTVEpA9wxB0VwYHtYoSti80UPZiL1cr0FKJlpco" . PHP_EOL .
        "Sq2f+EzqJMr0xRTUd6JsG67E45pvbf9eDvYI3FWOvX9khGJzXQUU0+kZu5lt3Vgg" . PHP_EOL .
        "mEgCWht6xeUBnMbw4ANLC5vWBxn1PeyNt4RE6uzAOhuOC0pifRQwbd/AR0/g6bBW" . PHP_EOL .
        "Iv48zmsomkx35hXVOgipbAX1naGLZoU38iAyVo+GLz7NfxuRtw3MltuEd53pRY6u" . PHP_EOL .
        "Qm1qTsGKLWXUysYc98NUiZS3O+l+/+tLv65hnnDx0cxqv9y1R7S8ZftcckFzM1iH" . PHP_EOL .
        "1ToLyixUOmACL6IRXZokDUOsxrXbLwnOORNB" . PHP_EOL .
        "-----END CERTIFICATE-----";
    }

    /**
     * @group InstancePrincipalsRequired
     */
    public function testFederationClient_AutoDetectAll()
    {
        $fc = new X509FederationClient(FederationClientTest::$sessionKeyProvider);
    }

    /**
     * @group InstancePrincipalsRequired
     */
    public function testFederationClient_AutoDetectCertificates()
    {
        $fc = new X509FederationClient(
            FederationClientTest::$sessionKeyProvider,
            ["federationEndpoint" => "https://auth.us-phoenix-1.oraclecloud.com/v1/x509"]
        );
    }

    /**
     * @group InstancePrincipalsRequired
     */
    public function testFederationClient_AutoDetectNothing()
    {
        $ls = new TestX509CertificateSupplier();
        $is = new TestX509CertificateSupplier();

        $fc = new X509FederationClient(
            FederationClientTest::$sessionKeyProvider,
            [
                "federationEndpoint" => "https://auth.us-phoenix-1.oraclecloud.com/v1/x509",
                "tenancyId" => "ocid1.tenancy.oc1..aaaaaaaacqp432hpa5oc2kvxm4kpwbkodfru4okbw2obkcdob5zuegi4rwxq",
                "leafCertificateSupplier" => $ls,
                "intermediateCertificateSuppliers" => $is
            ]
        );
    }

    /**
     * @group InstancePrincipalsRequired
     */
    public function testFederationClient_AutoDetectNothing_RegionSet()
    {
        $ls = new TestX509CertificateSupplier();
        $is = new TestX509CertificateSupplier();

        $fc = new X509FederationClient(
            FederationClientTest::$sessionKeyProvider,
            [
                "region" => "us-phoenix-1",
                "tenancyId" => "ocid1.tenancy.oc1..aaaaaaaacqp432hpa5oc2kvxm4kpwbkodfru4okbw2obkcdob5zuegi4rwxq",
                "leafCertificateSupplier" => $ls,
                "intermediateCertificateSuppliers" => $is
            ]
        );
    }

    public function testFederationClient_unknownParameter()
    {
        $ls = new TestX509CertificateSupplier();
        $is = new TestX509CertificateSupplier();

        try {
            $fc = new X509FederationClient(
                FederationClientTest::$sessionKeyProvider,
                [
                    "federationEndpoint" => "https://auth.us-phoenix-1.oraclecloud.com/v1/x509",
                    "tenancyId" => "ocid1.tenancy.oc1..aaaaaaaacqp432hpa5oc2kvxm4kpwbkodfru4okbw2obkcdob5zuegi4rwxq",
                    "leafCertificateSupplier" => $ls,
                    "intermediateCertificateSuppliers" => $is,
                    "unknown" => "shouldThrow"
                ]
            );
            $this->fail("Should have thrown");
        } catch (InvalidArgumentException $iae) {
            // expected
        } catch (Exception $e) {
            $this->fail("Should have thrown an InvalidArgumentException");
        }
    }

    /**
     * @group InstancePrincipalsRequired
     */
    public function testFederationClient_GetSecurityToken_AutoDetectAll()
    {
        $fc = new TestX509FederationClient(FederationClientTest::$sessionKeyProvider);
        $token = $fc->getSecurityToken();
        $this->assertTrue(strlen($token) > 0);
        $sta = $fc->getSecurityTokenAdapter();
        $this->assertTrue($sta->isValid());
        $jwt = $sta->getJwt();
        $this->assertNotNull($jwt);
        $this->assertEquals("sig", $sta->getStringClaim("use"));
    }

    public function testGetTenancyIdFromCertificate()
    {
        $tenancy_id = X509FederationClient::getTenancyIdFromCertificate(FederationClientTest::$cert);
        $this->assertEquals("ocid1.tenancy.oc1..aaaaaaaacqp432hpa5oc2kvxm4kpwbkodfru4okbw2obkcdob5zuegi4rwxq", $tenancy_id);
    }

    public function testBase64EncodeIfNecessary()
    {
        $notChunked = X509FederationClient::base64EncodeIfNecessary(FederationClientTest::$cert);
        $this->assertNotContains("-----BEGIN CERTIFICATE-----", $notChunked);
        $this->assertNotContains("-----END CERTIFICATE-----", $notChunked);
        $parts = explode(PHP_EOL, $notChunked);
        $this->assertEquals(1, count($parts));
        $parts = explode(PHP_EOL, FederationClientTest::$cert);
        $this->assertTrue(count($parts) > 1);
        $this->assertContains("-----BEGIN CERTIFICATE-----", FederationClientTest::$cert);
        $this->assertContains("-----END CERTIFICATE-----", FederationClientTest::$cert);

        // check other line breaks: \n
        $cert = str_replace(PHP_EOL, "\n", FederationClientTest::$cert);
        $notChunked = X509FederationClient::base64EncodeIfNecessary($cert);
        $this->assertNotContains("-----BEGIN CERTIFICATE-----", $notChunked);
        $this->assertNotContains("-----END CERTIFICATE-----", $notChunked);
        $parts = explode(PHP_EOL, $notChunked);
        $this->assertNotContains("-----BEGIN CERTIFICATE-----", $notChunked);
        $this->assertNotContains("-----END CERTIFICATE-----", $notChunked);

        // check other line breaks: \r
        $cert = str_replace(PHP_EOL, "\r", FederationClientTest::$cert);
        $notChunked = X509FederationClient::base64EncodeIfNecessary($cert);
        $this->assertNotContains("-----BEGIN CERTIFICATE-----", $notChunked);
        $this->assertNotContains("-----END CERTIFICATE-----", $notChunked);
        $parts = explode(PHP_EOL, $notChunked);
        $this->assertNotContains("-----BEGIN CERTIFICATE-----", $notChunked);
        $this->assertNotContains("-----END CERTIFICATE-----", $notChunked);

        // check other line breaks: \n\r
        $cert = str_replace(PHP_EOL, "\n\r", FederationClientTest::$cert);
        $notChunked = X509FederationClient::base64EncodeIfNecessary($cert);
        $this->assertNotContains("-----BEGIN CERTIFICATE-----", $notChunked);
        $this->assertNotContains("-----END CERTIFICATE-----", $notChunked);
        $parts = explode(PHP_EOL, $notChunked);
        $this->assertNotContains("-----BEGIN CERTIFICATE-----", $notChunked);
        $this->assertNotContains("-----END CERTIFICATE-----", $notChunked);

        // check other line breaks: \r\n
        $cert = str_replace(PHP_EOL, "\r\n", FederationClientTest::$cert);
        $notChunked = X509FederationClient::base64EncodeIfNecessary($cert);
        $this->assertNotContains("-----BEGIN CERTIFICATE-----", $notChunked);
        $this->assertNotContains("-----END CERTIFICATE-----", $notChunked);
        $parts = explode(PHP_EOL, $notChunked);
        $this->assertNotContains("-----BEGIN CERTIFICATE-----", $notChunked);
        $this->assertNotContains("-----END CERTIFICATE-----", $notChunked);
    }
}

class TestX509CertificateSupplier implements X509CertificateSupplierInterface
{
    public function getKeyPair()
    {
        return null;
    }
}

class TestX509FederationClient extends X509FederationClient
{
    public function __construct(SessionKeySupplierInterface $sessionKeySupplier, $params=[])
    {
        parent::__construct($sessionKeySupplier, $params);
    }

    public function getSecurityTokenAdapter()
    {
        return parent::getSecurityTokenAdapter();
    }
}
