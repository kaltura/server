<?php

use PHPUnit\Framework\TestCase;
use Oracle\Oci\Common\HttpUtils;

class HttpUtilsTest extends TestCase
{
    public function testAddToArray_empty()
    {
        $value = "abc";
        $queryMap = [];
        HttpUtils::addToArray($queryMap, "paramName", $value);
        $this->assertEquals(["paramName" => $value], $queryMap);
    }

    public function testAddToArray_single()
    {
        $value = "abc";
        $queryMap = ["paramName" => "old"];
        HttpUtils::addToArray($queryMap, "paramName", $value);
        $this->assertEquals(["paramName" => ["old", $value]], $queryMap);
    }

    public function testAddToArray_array()
    {
        $value = "abc";
        $queryMap = ["paramName" => ["old1", "old2"]];
        HttpUtils::addToArray($queryMap, "paramName", $value);
        $this->assertEquals(["paramName" => ["old1", "old2", $value]], $queryMap);
    }

    public function testattemptEncodeParam()
    {
        $this->assertEquals("abc", HttpUtils::attemptEncodeParam("abc"));

        // TODO: check if this is required, or if Guzzle escapes it
        // $this->assertEquals("%251%253D%253F%2540%255B%255D%2541%20aaaa", HttpUtils::attemptEncodeParam("%1%3D%3F%40%5B%5D%41 aaaa"));

        $this->assertEquals("1", HttpUtils::attemptEncodeParam(1));

        $dt = new DateTime(); // now
        $this->assertEquals($dt->format(HttpUtils::$RFC3339_EXTENDED), HttpUtils::attemptEncodeParam($dt));
    }

    public function testEncodeArray_null()
    {
        $array = null;
        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "csv");
        $this->assertEquals([], $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "ssv");
        $this->assertEquals([], $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "tsv");
        $this->assertEquals([], $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "pipes");
        $this->assertEquals([], $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "multi");
        $this->assertEquals([], $queryMap);
    }

    public function testEncodeArray_empty()
    {
        $array = [];
        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "csv");
        $this->assertEquals([], $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "ssv");
        $this->assertEquals([], $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "tsv");
        $this->assertEquals([], $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "pipes");
        $this->assertEquals([], $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "multi");
        $this->assertEquals([], $queryMap);
    }

    public function testEncodeArray_single()
    {
        $array = ["abc"];
        $expectedQueryMap = ["paramName" => "abc"];
        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "csv");
        $this->assertEquals($expectedQueryMap, $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "ssv");
        $this->assertEquals($expectedQueryMap, $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "tsv");
        $this->assertEquals($expectedQueryMap, $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "pipes");
        $this->assertEquals($expectedQueryMap, $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "multi");
        $this->assertEquals($expectedQueryMap, $queryMap);
    }

    public function testEncodeArray_single_withExistingKey()
    {
        $array = ["abc"];
        $expectedQueryMap = ["paramName" => ["old", "abc"]];
        $queryMap = ["paramName" => "old"];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "csv");
        $this->assertEquals($expectedQueryMap, $queryMap);

        $queryMap = ["paramName" => "old"];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "ssv");
        $this->assertEquals($expectedQueryMap, $queryMap);

        $queryMap = ["paramName" => "old"];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "tsv");
        $this->assertEquals($expectedQueryMap, $queryMap);

        $queryMap = ["paramName" => "old"];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "pipes");
        $this->assertEquals($expectedQueryMap, $queryMap);

        $queryMap = ["paramName" => "old"];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "multi");
        $this->assertEquals($expectedQueryMap, $queryMap);
    }

    public function testEncodeArray_singleDateTime()
    {
        $dt = new DateTime(); // now
        $array = [$dt];
        $expected = $dt->format(HttpUtils::$RFC3339_EXTENDED);
        $expectedQueryMap = ["paramName" => $expected];

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "csv");
        $this->assertEquals($expectedQueryMap, $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "ssv");
        $this->assertEquals($expectedQueryMap, $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "tsv");
        $this->assertEquals($expectedQueryMap, $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "pipes");
        $this->assertEquals($expectedQueryMap, $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "multi");
        $this->assertEquals($expectedQueryMap, $queryMap);
    }

    public function testEncodeArray_multiple()
    {
        $dt = new DateTime(); // now
        $array = ["abc", $dt, 1];
        $expected = $dt->format(HttpUtils::$RFC3339_EXTENDED);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "csv");
        $this->assertEquals(["paramName" => "abc,$expected,1"], $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "ssv");
        $this->assertEquals(["paramName" => "abc $expected 1"], $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "tsv");
        $this->assertEquals(["paramName" => "abc\t{$expected}\t1"], $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "pipes");
        $this->assertEquals(["paramName" => "abc|$expected|1"], $queryMap);

        $queryMap = [];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "multi");
        $this->assertEquals(["paramName" => ["abc", $expected, "1"]], $queryMap);
    }

    public function testEncodeArray_multiple_withExistingKey()
    {
        $dt = new DateTime(); // now
        $array = ["abc", $dt, 1];
        $expected = $dt->format(HttpUtils::$RFC3339_EXTENDED);

        $queryMap = ["paramName" => "old"];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "csv");
        $this->assertEquals(["paramName" => ["old", "abc,$expected,1"]], $queryMap);

        $queryMap = ["paramName" => "old"];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "ssv");
        $this->assertEquals(["paramName" => ["old", "abc $expected 1"]], $queryMap);

        $queryMap = ["paramName" => "old"];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "tsv");
        $this->assertEquals(["paramName" => ["old", "abc\t{$expected}\t1"]], $queryMap);

        $queryMap = ["paramName" => "old"];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "pipes");
        $this->assertEquals(["paramName" => ["old", "abc|$expected|1"]], $queryMap);

        $queryMap = ["paramName" => "old"];
        HttpUtils::encodeArray($queryMap, "paramName", $array, "multi");
        $this->assertEquals(["paramName" => ["old", "abc", $expected, "1"]], $queryMap);
    }

    public function testEncodeMap_null()
    {
        $map = null;

        $queryMap = [];
        HttpUtils::encodeMap($queryMap, "paramName", "prefix.", $map);
        $this->assertEquals([], $queryMap);
    }

    public function testEncodeMap_empty()
    {
        $map = [];

        $queryMap = [];
        HttpUtils::encodeMap($queryMap, "paramName", "prefix.", $map);
        $this->assertEquals([], $queryMap);
    }

    public function testEncodeMap_single()
    {
        $map = ["key" => "value"];

        $queryMap = [];
        HttpUtils::encodeMap($queryMap, "paramName", "prefix.", $map);
        $this->assertEquals(["prefix.key" => "value"], $queryMap);
    }

    public function testEncodeMap_singleDateTime()
    {
        $dt = new DateTime(); // now
        $map = ["key" => $dt];
        $expected = $dt->format(HttpUtils::$RFC3339_EXTENDED);

        $queryMap = [];
        HttpUtils::encodeMap($queryMap, "paramName", "prefix.", $map);
        $this->assertEquals(["prefix.key" => $expected], $queryMap);
    }

    public function testEncodeMap_single_nullPrefix()
    {
        $map = ["key" => "value"];

        $queryMap = [];
        HttpUtils::encodeMap($queryMap, "paramName", (string) null, $map);
        $this->assertEquals(["key" => "value"], $queryMap);
    }

    public function testEncodeMap_singleDateTime_nullPrefix()
    {
        $dt = new DateTime(); // now
        $map = ["key" => $dt];
        $expected = $dt->format(HttpUtils::$RFC3339_EXTENDED);

        $queryMap = [];
        HttpUtils::encodeMap($queryMap, "paramName", (string) null, $map);
        $this->assertEquals(["key" => $expected], $queryMap);
    }

    public function testEncodeMap_multiple()
    {
        $dt = new DateTime(); // now
        $map = [
            "key1" => "abc",
            "key2" => $dt,
            "key3" => 1,
            "key4" => ["abc", $dt, 1]];
        $expected = $dt->format(HttpUtils::$RFC3339_EXTENDED);

        $queryMap = [];
        HttpUtils::encodeMap($queryMap, "paramName", "prefix.", $map);
        $this->assertEquals([
            "prefix.key1" => "abc",
            "prefix.key2" => $expected,
            "prefix.key3" => "1",
            "prefix.key4" => ["abc", $expected, "1"]], $queryMap);
    }

    public function testEncodeMap_multiple2()
    {
        $map = [
            "a" => "1",
            "b" => ["2", "3"]];

        $queryMap = [];
        HttpUtils::encodeMap($queryMap, "paramName", "prefix.", $map);
        $this->assertEquals([
            "prefix.a" => "1",
            "prefix.b" => ["2", "3"]], $queryMap);
    }

    public function testEncodeMap_multiple_nullPrefix()
    {
        $dt = new DateTime(); // now
        $map = [
            "key1" => "abc",
            "key2" => $dt,
            "key3" => 1,
            "key4" => ["abc", $dt, 1]];
        $expected = $dt->format(HttpUtils::$RFC3339_EXTENDED);

        $queryMap = [];
        HttpUtils::encodeMap($queryMap, "paramName", (string) null, $map);
        $this->assertEquals([
            "key1" => "abc",
            "key2" => $expected,
            "key3" => "1",
            "key4" => ["abc", $expected, "1"]], $queryMap);
    }

    public function testOrNull()
    {
        $this->assertEquals(null, HttpUtils::orNull([], "paramName"));
        $this->assertEquals("paramValue", HttpUtils::orNull(["paramName" => "paramValue"], "paramName"));
    }

    public function testOrNull_required()
    {
        $this->assertEquals("paramValue", HttpUtils::orNull(["paramName" => "paramValue"], "paramName", true));
        try {
            HttpUtils::orNull([], "paramName", true);
            $this->fail("Should have thrown");
        } catch (InvalidArgumentException $e) {
            // expected
        }
    }

    public function testQueryMapToString()
    {
        $this->assertEquals("", HttpUtils::queryMapToString([]));
        $this->assertEquals("?a=1&b=2&b=3", HttpUtils::queryMapToString(["a" => 1, "b" => [2, 3]]));
    }
}
