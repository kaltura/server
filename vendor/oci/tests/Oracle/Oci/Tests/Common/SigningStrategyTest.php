<?php

use Oracle\Oci\Common\Constants;
use Oracle\Oci\Common\ExcludeBodySigningStrategy;
use Oracle\Oci\Common\FederationSigningStrategy;
use Oracle\Oci\Common\ObjectStorageSigningStrategy;
use Oracle\Oci\Common\SigningStrategies;
use Oracle\Oci\Common\StandardSigningStrategy;
use PHPUnit\Framework\TestCase;

class SigningStrategyTest extends TestCase
{
    public function testGetSigningStrategy()
    {
        $this->assertEquals(StandardSigningStrategy::getSingleton(), SigningStrategies::get("standard"));
        $this->assertEquals(StandardSigningStrategy::getSingleton(), SigningStrategies::get("STANDARD"));
        $this->assertEquals(StandardSigningStrategy::getSingleton(), SigningStrategies::get("Standard"));

        $this->assertEquals(ExcludeBodySigningStrategy::getSingleton(), SigningStrategies::get("exclude_body"));
        $this->assertEquals(ObjectStorageSigningStrategy::getSingleton(), SigningStrategies::get("object_storage"));
        $this->assertEquals(FederationSigningStrategy::getSingleton(), SigningStrategies::get("federation"));

        try {
            $this->assertEquals(FederationSigningStrategy::getSingleton(), SigningStrategies::get("bogey"));
            $this->fail("Should have thrown");
        } catch (InvalidArgumentException $iae) {
            // expected
        } catch (Exception $e) {
            $this->fail("Should have thrown an InvalidArgumentException");
        }
    }

    public function testFederationPost()
    {
        $ss = SigningStrategies::get("federation");

        $requiredHeaders = $ss->getRequiredSigningHeaders("post");

        $this->assertFalse(array_search(Constants::HOST_HEADER_NAME, $requiredHeaders) !== false);
    }

    public function testObjectStoragePut()
    {
        $ss = SigningStrategies::get("object_storage");

        $requiredHeaders = $ss->getRequiredSigningHeaders("put");

        $this->assertTrue(array_search(Constants::HOST_HEADER_NAME, $requiredHeaders) !== false);
        $this->assertFalse(array_search(Constants::CONTENT_LENGTH_HEADER_NAME, $requiredHeaders) !== false);
        $this->assertFalse(array_search(Constants::CONTENT_TYPE_HEADER_NAME, $requiredHeaders) !== false);
        $this->assertFalse(array_search(Constants::X_CONTENT_SHA256_HEADER_NAME, $requiredHeaders) !== false);
    }

    public function testStandardPut()
    {
        $ss = SigningStrategies::get("standard");

        $requiredHeaders = $ss->getRequiredSigningHeaders("put");

        $this->assertTrue(array_search(Constants::HOST_HEADER_NAME, $requiredHeaders) !== false);
        $this->assertTrue(array_search(Constants::CONTENT_LENGTH_HEADER_NAME, $requiredHeaders) !== false);
        $this->assertTrue(array_search(Constants::CONTENT_TYPE_HEADER_NAME, $requiredHeaders) !== false);
        $this->assertTrue(array_search(Constants::X_CONTENT_SHA256_HEADER_NAME, $requiredHeaders) !== false);
    }

    public function testExcludeBodyPut()
    {
        $ss = SigningStrategies::get("exclude_body");

        $requiredHeaders = $ss->getRequiredSigningHeaders("put");

        $this->assertTrue(array_search(Constants::HOST_HEADER_NAME, $requiredHeaders) !== false);
        $this->assertFalse(array_search(Constants::CONTENT_LENGTH_HEADER_NAME, $requiredHeaders) !== false);
        $this->assertFalse(array_search(Constants::CONTENT_TYPE_HEADER_NAME, $requiredHeaders) !== false);
        $this->assertFalse(array_search(Constants::X_CONTENT_SHA256_HEADER_NAME, $requiredHeaders) !== false);
    }

    public function testObjectStoragePost()
    {
        $ss = SigningStrategies::get("object_storage");

        $requiredHeaders = $ss->getRequiredSigningHeaders("post");

        $this->assertTrue(array_search(Constants::HOST_HEADER_NAME, $requiredHeaders) !== false);
        $this->assertTrue(array_search(Constants::CONTENT_LENGTH_HEADER_NAME, $requiredHeaders) !== false);
        $this->assertTrue(array_search(Constants::CONTENT_TYPE_HEADER_NAME, $requiredHeaders) !== false);
        $this->assertTrue(array_search(Constants::X_CONTENT_SHA256_HEADER_NAME, $requiredHeaders) !== false);
    }

    public function testStandardPost()
    {
        $ss = SigningStrategies::get("standard");

        $requiredHeaders = $ss->getRequiredSigningHeaders("post");

        $this->assertTrue(array_search(Constants::HOST_HEADER_NAME, $requiredHeaders) !== false);
        $this->assertTrue(array_search(Constants::CONTENT_LENGTH_HEADER_NAME, $requiredHeaders) !== false);
        $this->assertTrue(array_search(Constants::CONTENT_TYPE_HEADER_NAME, $requiredHeaders) !== false);
        $this->assertTrue(array_search(Constants::X_CONTENT_SHA256_HEADER_NAME, $requiredHeaders) !== false);
    }

    public function testExcludeBodyPost()
    {
        $ss = SigningStrategies::get("exclude_body");

        $requiredHeaders = $ss->getRequiredSigningHeaders("post");

        $this->assertTrue(array_search(Constants::HOST_HEADER_NAME, $requiredHeaders) !== false);
        $this->assertFalse(array_search(Constants::CONTENT_LENGTH_HEADER_NAME, $requiredHeaders) !== false);
        $this->assertFalse(array_search(Constants::CONTENT_TYPE_HEADER_NAME, $requiredHeaders) !== false);
        $this->assertFalse(array_search(Constants::X_CONTENT_SHA256_HEADER_NAME, $requiredHeaders) !== false);
    }
}
