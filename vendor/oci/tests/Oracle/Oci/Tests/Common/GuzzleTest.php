<?php

use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

class GuzzleTest extends TestCase
{
    public function testPathEscaping()
    {
        // Not escaped: a" through "z", "A" through "Z" and "0" through "9"
        $this->doesNotGetEscaped("ABCXYZabcxyz0123456789");

        // Not escaped: unreserved characters ".", "-", "~", and "_"
        $this->doesNotGetEscaped(".-~_");

        // Not escaped: general delimiters "@" and ":"
        $this->doesNotGetEscaped("@:");

        // Not escaped: subdelimiters "!", "$", "&", "'", "(", ")", "*", "+", ",", ";", and "="
        $this->doesNotGetEscaped("!$&'()*+,;=");

        // Escaped: space character " " is converted into "%20"
        $this->assertEquals("space%20is%20escaped", $this->escapePath("space is escaped"));
    }

    public function testPathEscaping_other()
    {
        // All other characters are converted into UTF-8 encoding, and each UTF-8 byte is
        // represented as "%XY", where "XY" is the two-digit, uppercase, hexadecimal representation of the byte.
        $this->assertEquals("%00", $this->escapePath(utf8_decode("\x00")));
        $this->assertEquals("%7F", $this->escapePath(utf8_decode("\x7f")));
        $this->assertEquals("%C2%80", $this->escapePath(utf8_decode("\xC2\x80")));

        // TODO: Guzzle has trouble with non-overlong 2-byte
        // $this->assertEquals("%DF%BF", $this->escapePath(utf8_decode("\xDF\xBF"))); // xx-11111,x-111111

        // TODO: Guzzle has trouble with excluding overlongs
        // $this->assertEquals("%E0%A0%80", $this->escapePath(utf8_decode("\xE0\xA0\x80"))); // xxx-0000,x-100000,x-00,0000

        // TODO: Guzzle has trouble with straight 3-byte
        // $this->assertEquals("%EF%BF%BF", $this->escapePath(utf8_decode("\xEF\xBF\xBF"))); // xxx-1111,x-111111,x-11,1111
    }

    private function doesNotGetEscaped($placeholder)
    {
        $this->assertEquals($placeholder, $this->escapePath($placeholder));
    }

    private static function escapePath($placeholder)
    {
        $prefix = "https://objectstorage.us-phoenix-1.oraclecloud.com/n/";
        $path = $prefix . "{placeholder}";
        $path = str_replace('{placeholder}', utf8_encode($placeholder), $path);

        $r = new Request("get", $path);

        return substr($r->getUri(), strlen($prefix));
    }
}
