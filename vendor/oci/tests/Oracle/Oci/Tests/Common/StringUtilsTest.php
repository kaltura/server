<?php

namespace Oracle\Oci\Common;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class StringUtilsTest extends TestCase
{
    public function testBase64Url_RoundTrip()
    {
        $input = hex2bin("fae789d2af75b795ce9c560ff1769cdc") .
        "How do you like to go up in a swing? Up in the air so blue?\n" .
        "by Robert Louis Stevenson";
        $b64url = StringUtils::base64url_encode($input);
        $output = StringUtils::base64url_decode($b64url, true);
        $this->assertEquals($input, $output);
        $this->assertNotContains("/", $b64url);
        $this->assertNotContains("+", $b64url);

        $b64 = base64_encode($input);
        $this->assertContains("/", $b64);
        $this->assertContains("+", $b64);
    }

    public function testBase64_RoundTrip()
    {
        $input = "Night time shows us where they are.";
        $b64 = base64_encode($input);
        $output = base64_decode($b64, true);
        $this->assertEquals($input, $output);
    }

    public function testCheckType_NotAllowed()
    {
        try {
            StringUtils::checkType("unknown", 123, []);
            $this->fail("Should have thrown an InvalidArgumentException");
        } catch (InvalidArgumentException $iae) {
            // expected
        }
    }

    public function testCheckType_BadType_Single()
    {
        try {
            StringUtils::checkType("k", 123, [
                "k" => "string"
            ]);
            $this->fail("Should have thrown an InvalidArgumentException");
        } catch (InvalidArgumentException $iae) {
            // expected
        }
        try {
            StringUtils::checkType("k", new StringUtilsTestClass(), [
                "k" => StringUtilsTestChildClass::class
            ]);
            $this->fail("Should have thrown an InvalidArgumentException");
        } catch (InvalidArgumentException $iae) {
            // expected
        }
    }

    public function testCheckType_BadType_Multiple()
    {
        try {
            StringUtils::checkType("k", 123, [
                "k" => ["string", OtherStringUtilsTestClass::class]
            ]);
            $this->fail("Should have thrown an InvalidArgumentException");
        } catch (InvalidArgumentException $iae) {
            // expected
        }
        try {
            StringUtils::checkType("k", new StringUtilsTestClass(), [
                "k" => ["string", OtherStringUtilsTestClass::class]
            ]);
            $this->fail("Should have thrown an InvalidArgumentException");
        } catch (InvalidArgumentException $iae) {
            // expected
        }
        try {
            StringUtils::checkType("k", new StringUtilsTestClass(), [
                "k" => ["string", StringUtilsTestChildClass::class]
            ]);
            $this->fail("Should have thrown an InvalidArgumentException");
        } catch (InvalidArgumentException $iae) {
            // expected
        }
    }

    public function testCheckType_GoodType_Single()
    {
        StringUtils::checkType("k", "foo", [
            "k" => null
        ]);
        StringUtils::checkType("k", "foo", [
            "k" => "string"
        ]);
        StringUtils::checkType("k", new StringUtilsTestChildClass(), [
            "k" => StringUtilsTestChildClass::class
        ]);
        StringUtils::checkType("k", new StringUtilsTestChildClass(), [
            "k" => StringUtilsTestClass::class
        ]);
    }

    public function testCheckType_GoodType_Multiple()
    {
        StringUtils::checkType("k", "foo", [
            "k" => ["string", OtherStringUtilsTestClass::class]
        ]);
        StringUtils::checkType("k", new StringUtilsTestClass(), [
            "k" => ["string", StringUtilsTestClass::class]
        ]);
        StringUtils::checkType("k", new StringUtilsTestChildClass(), [
            "k" => ["string", StringUtilsTestClass::class]
        ]);
    }
    public function testIsType()
    {
        $this->assertTrue(StringUtils::isType("foo", "string"));
        $this->assertFalse(StringUtils::isType(1, "string"));
        $this->assertFalse(StringUtils::isType(["foo", 1], "string"));
        $this->assertFalse(StringUtils::isType(new StringUtilsTestClass(), "string"));

        $this->assertFalse(StringUtils::isType("foo", StringUtilsTestClass::class));
        $this->assertFalse(StringUtils::isType(1, StringUtilsTestClass::class));
        $this->assertFalse(StringUtils::isType(["foo", 1], StringUtilsTestClass::class));
        $this->assertTrue(StringUtils::isType(new StringUtilsTestClass(), StringUtilsTestClass::class));
        $this->assertTrue(StringUtils::isType(new StringUtilsTestChildClass(), StringUtilsTestClass::class));
        $this->assertFalse(StringUtils::isType(new OtherStringUtilsTestClass(), StringUtilsTestClass::class));

        $this->assertFalse(StringUtils::isType(new StringUtilsTestClass(), StringUtilsTestChildClass::class));
    }
}

class StringUtilsTestClass
{
}

class StringUtilsTestChildClass extends StringUtilsTestClass
{
}

class OtherStringUtilsTestClass
{
}
