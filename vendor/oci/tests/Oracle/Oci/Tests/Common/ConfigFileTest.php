<?php

use PHPUnit\Framework\TestCase;
use Oracle\Oci\Common\ConfigFile;

class ConfigFileTest extends TestCase
{
    private static $cf1 = [
        "# This is a comment",
        "   default     = defaultValue",
        "    ",
        "",
        "  [ OTHER   ]",
        "otherKey = otherValue",
        "otherKey2 = otherValue2",
        "defaultOverridden = overriddenValue",
        "",
        "   [ DEFAULT   ]   ",
        "defaultOverridden = notOverriddenValue",
        "",
        "[OTHER]",
        "otherKey3 = otherValue3"
    ];

    public function testLoadFromStringArray_defaultProfile()
    {
        $cf = ConfigFile::loadFromStringArray(ConfigFileTest::$cf1);

        $this->assertEquals("defaultValue", $cf->get("default"));
        $this->assertEquals("notOverriddenValue", $cf->get("defaultOverridden"));
        $this->assertEquals(null, $cf->get("otherKey"));
        $this->assertEquals(null, $cf->get("otherKey2"));
        $this->assertEquals(null, $cf->get("otherKey3"));

        $cfStr = strval($cf);

        $cf = ConfigFile::loadFromString($cfStr);

        $this->assertEquals("defaultValue", $cf->get("default"));
        $this->assertEquals("notOverriddenValue", $cf->get("defaultOverridden"));
        $this->assertEquals(null, $cf->get("otherKey"));
        $this->assertEquals(null, $cf->get("otherKey2"));
        $this->assertEquals(null, $cf->get("otherKey3"));
    }

    public function testLoadFromStringArray_otherProfile()
    {
        $cf = ConfigFile::loadFromStringArray(ConfigFileTest::$cf1, "OTHER");

        $this->assertEquals("defaultValue", $cf->get("default"));
        $this->assertEquals("overriddenValue", $cf->get("defaultOverridden"));
        $this->assertEquals("otherValue", $cf->get("otherKey"));
        $this->assertEquals("otherValue2", $cf->get("otherKey2"));
        $this->assertEquals("otherValue3", $cf->get("otherKey3"));

        $cfStr = strval($cf);

        $cf = ConfigFile::loadFromString($cfStr, "OTHER");

        $this->assertEquals("defaultValue", $cf->get("default"));
        $this->assertEquals("overriddenValue", $cf->get("defaultOverridden"));
        $this->assertEquals("otherValue", $cf->get("otherKey"));
        $this->assertEquals("otherValue2", $cf->get("otherKey2"));
        $this->assertEquals("otherValue3", $cf->get("otherKey3"));
    }
}
