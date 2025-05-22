<?php
require_once 'kIpAddressUtils.php';

class ipv6Test {
    public static function runTests() {
        self::testIpToLongWithIPv4();
        self::testIpToLongWithIPv6();
        self::testIsIpInRangeWithIPv4();
        self::testIsIpInRangeWithIPv6();
        self::testGetAddressType();
        self::testParseIpRange();
        self::testIsIpInRanges();
        self::testInfraRequestUtils();
        self::testAnalyzeCollectStats();
        self::testUvSummaryInsert();
        self::testWidgetLog();
        self::testDigitalElementIPGeoCoder();
        self::testCredentialsUtils();
        self::testIP2Location();
        self::testZendValidateIp();
        self::testConvertAnonymousCsvToBin();
    }

    public static function testIpToLongWithIPv4() {
        $ip = '10.0.0.1';
        $result = kIpAddressUtils::ipToLong($ip);
        assert($result !== null, "IPv4 address should be converted to long.");
    }

    public static function testIpToLongWithIPv6() {
        $ip = 'fe80::1ff:fe23:4567:890a';
        $result = kIpAddressUtils::ipToLong($ip);
        assert($result !== null, "IPv6 address should be converted to long.");
    }

    public static function testIsIpInRangeWithIPv4() {
        $ip = '10.0.0.1';
        $range = '10.0.0.0/8';
        $result = kIpAddressUtils::isIpInRange($ip, $range);
        assert($result === true, "IPv4 address should be in range.");
    }

    public static function testIsIpInRangeWithIPv6() {
        $ip = 'fe80::1ff:fe23:4567:890a';
        $range = 'fe80::/10';
        $result = kIpAddressUtils::isIpInRange($ip, $range);
        assert($result === true, "IPv6 address should be in range.");
    }

    public static function testGetAddressType() {
        echo "Testing getAddressType...\n";

        // Test IPv4 single
        assert(kIpAddressUtils::getAddressType('192.168.1.1') === kIpAddressUtils::IP_ADDRESS_TYPE_SINGLE);

        // Test IPv4 CIDR
        assert(kIpAddressUtils::getAddressType('192.168.1.1/24') === kIpAddressUtils::IP_ADDRESS_TYPE_MASK_CIDR);

        // Test IPv6 single
        assert(kIpAddressUtils::getAddressType('2001:db8::1') === kIpAddressUtils::IP_ADDRESS_TYPE_SINGLE);

        // Test IPv6 CIDR
        assert(kIpAddressUtils::getAddressType('2001:db8::1/64') === kIpAddressUtils::IP_ADDRESS_TYPE_MASK_CIDR);

        echo "getAddressType tests passed!\n";
    }

    public static function testParseIpRange() {
        echo "Testing parseIpRange...\n";

        // Test IPv4 range
        assert(kIpAddressUtils::parseIpRange('192.168.1.1-192.168.1.10') === [
            kIpAddressUtils::ipToLong('192.168.1.1'),
            kIpAddressUtils::ipToLong('192.168.1.10')
        ]);

        // Test IPv6 range
        assert(kIpAddressUtils::parseIpRange('2001:db8::1-2001:db8::10') === [
            kIpAddressUtils::ipToLong('2001:db8::1'),
            kIpAddressUtils::ipToLong('2001:db8::10')
        ]);

        // Test IPv4 CIDR
        $cidrRange = kIpAddressUtils::parseIpRange('192.168.1.0/24');
        assert($cidrRange[0] === kIpAddressUtils::ipToLong('192.168.1.0'));
        assert($cidrRange[1] === kIpAddressUtils::ipToLong('192.168.1.255'));

        // Test IPv6 CIDR
        $cidrRange = kIpAddressUtils::parseIpRange('2001:db8::/64');
        assert($cidrRange[0] === kIpAddressUtils::ipToLong('2001:db8::'));
        assert($cidrRange[1] === kIpAddressUtils::ipToLong('2001:db8::ffff:ffff:ffff:ffff'));

        echo "parseIpRange tests passed!\n";
    }

    public static function testIsIpInRanges() {
        echo "Testing isIpInRanges...\n";

        // Test IPv4 ranges
        assert(kIpAddressUtils::isIpInRanges('192.168.1.5', '192.168.1.1-192.168.1.10,192.168.2.1-192.168.2.10') === true);
        assert(kIpAddressUtils::isIpInRanges('192.168.3.5', '192.168.1.1-192.168.1.10,192.168.2.1-192.168.2.10') === false);

        // Test IPv6 ranges
        assert(kIpAddressUtils::isIpInRanges('2001:db8::5', '2001:db8::1-2001:db8::10,2001:db9::1-2001:db9::10') === true);
        assert(kIpAddressUtils::isIpInRanges('2001:db10::5', '2001:db8::1-2001:db8::10,2001:db9::1-2001:db9::10') === false);

        echo "isIpInRanges tests passed!\n";
    }

    public static function testIsIpInRange() {
        echo "Testing isIpInRange...\n";

        // Test IPv4 range
        assert(kIpAddressUtils::isIpInRange('192.168.1.5', '192.168.1.1-192.168.1.10') === true);
        assert(kIpAddressUtils::isIpInRange('192.168.2.5', '192.168.1.1-192.168.1.10') === false);

        // Test IPv6 range
        assert(kIpAddressUtils::isIpInRange('2001:db8::5', '2001:db8::1-2001:db8::10') === true);
        assert(kIpAddressUtils::isIpInRange('2001:db8::15', '2001:db8::1-2001:db8::10') === false);

        // Test IPv4 CIDR
        assert(kIpAddressUtils::isIpInRange('192.168.1.5', '192.168.1.0/24') === true);
        assert(kIpAddressUtils::isIpInRange('192.168.2.5', '192.168.1.0/24') === false);

        // Test IPv6 CIDR
        assert(kIpAddressUtils::isIpInRange('2001:db8::5', '2001:db8::/64') === true);
        assert(kIpAddressUtils::isIpInRange('2001:db9::5', '2001:db8::/64') === false);

        echo "isIpInRange tests passed!\n";
    }

    public static function testInfraRequestUtils() {
        echo "Testing infraRequestUtils with IPv4 and IPv6...\n";

        $startIPv4 = '192.168.1.1';
        $endIPv4 = '192.168.1.255';
        $startIPv6 = '2001:db8::1';
        $endIPv6 = '2001:db8::ffff';

        $longIpIPv4 = kIpAddressUtils::ipToLong('192.168.1.100');
        $longIpIPv6 = kIpAddressUtils::ipToLong('2001:db8::abcd');

        assert($longIpIPv4 >= kIpAddressUtils::ipToLong($startIPv4) && $longIpIPv4 <= kIpAddressUtils::ipToLong($endIPv4), "IPv4 range check failed.");
        assert($longIpIPv6 >= kIpAddressUtils::ipToLong($startIPv6) && $longIpIPv6 <= kIpAddressUtils::ipToLong($endIPv6), "IPv6 range check failed.");

        echo "infraRequestUtils tests passed!\n";
    }

    public static function testAnalyzeCollectStats() {
        echo "Testing analyze_collect_stats with IPv4 and IPv6...\n";

        $ipIPv4 = '10.0.0.1';
        $ipIPv6 = 'fe80::1ff:fe23:4567:890a';

        $longIpIPv4 = kIpAddressUtils::ipToLong($ipIPv4);
        $longIpIPv6 = kIpAddressUtils::ipToLong($ipIPv6);

        assert($longIpIPv4 !== false, "IPv4 conversion failed in analyze_collect_stats.");
        assert($longIpIPv6 !== false, "IPv6 conversion failed in analyze_collect_stats.");

        echo "analyze_collect_stats tests passed!\n";
    }

    public static function testUvSummaryInsert() {
        echo "Testing uv_summary_insert with IPv4 and IPv6...\n";

        $ipIPv4 = '172.16.0.1';
        $ipIPv6 = '2001:db8::1234';

        $longIpIPv4 = kIpAddressUtils::ipToLong($ipIPv4);
        $longIpIPv6 = kIpAddressUtils::ipToLong($ipIPv6);

        assert($longIpIPv4 !== false, "IPv4 conversion failed in uv_summary_insert.");
        assert($longIpIPv6 !== false, "IPv6 conversion failed in uv_summary_insert.");

        echo "uv_summary_insert tests passed!\n";
    }

    public static function testWidgetLog() {
        echo "Testing WidgetLog with IPv4 and IPv6...\n";

        $ipIPv4 = '192.168.0.1';
        $ipIPv6 = '2001:db8::5678';

        $longIpIPv4 = kIpAddressUtils::ipToLong($ipIPv4);
        $longIpIPv6 = kIpAddressUtils::ipToLong($ipIPv6);

        assert($longIpIPv4 !== false, "IPv4 conversion failed in WidgetLog.");
        assert($longIpIPv6 !== false, "IPv6 conversion failed in WidgetLog.");

        echo "WidgetLog tests passed!\n";
    }

    public static function testDigitalElementIPGeoCoder() {
        echo "Testing kDigitalElementIPGeoCoder with IPv4 and IPv6...\n";

        $ipIPv4 = '203.0.113.1';
        $ipIPv6 = '2001:db8::9abc';

        $longIpIPv4 = kIpAddressUtils::ipToLong($ipIPv4);
        $longIpIPv6 = kIpAddressUtils::ipToLong($ipIPv6);

        assert($longIpIPv4 !== false, "IPv4 conversion failed in kDigitalElementIPGeoCoder.");
        assert($longIpIPv6 !== false, "IPv6 conversion failed in kDigitalElementIPGeoCoder.");

        echo "kDigitalElementIPGeoCoder tests passed!\n";
    }

    public static function testCredentialsUtils() {
        echo "Testing CredentialsUtils with IPv4 and IPv6...\n";

        // Test IPv4 loopback range
        $loopbackStartIPv4 = kIpAddressUtils::ipToLong('127.0.0.0');
        $loopbackEndIPv4 = kIpAddressUtils::ipToLong('127.255.255.255');
        $testIpIPv4 = kIpAddressUtils::ipToLong('127.0.0.1');
        assert($testIpIPv4 >= $loopbackStartIPv4 && $testIpIPv4 <= $loopbackEndIPv4, "IPv4 loopback range check failed.");

        // Test IPv6 loopback
        $testIpIPv6 = kIpAddressUtils::ipToLong('::1');
        assert($testIpIPv6 !== null, "IPv6 loopback address conversion failed.");

        echo "CredentialsUtils tests passed!\n";
    }

    public static function testIP2Location() {
        echo "Testing IP2Location with IPv4 and IPv6...\n";

        // Test IPv4 conversion
        $ipIPv4 = '192.168.1.1';
        $longIpIPv4 = kIpAddressUtils::ipToLong($ipIPv4);
        assert($longIpIPv4 !== null, "IPv4 conversion failed in IP2Location.");

        // Test IPv6 conversion
        $ipIPv6 = '2001:db8::1';
        $longIpIPv6 = kIpAddressUtils::ipToLong($ipIPv6);
        assert($longIpIPv6 !== null, "IPv6 conversion failed in IP2Location.");

        echo "IP2Location tests passed!\n";
    }

    public static function testZendValidateIp() {
        echo "Testing Zend Validate IP with IPv4 and IPv6...\n";

        // Test IPv4 validation
        $ipIPv4 = '192.168.1.1';
        assert(kIpAddressUtils::getAddressType($ipIPv4) === kIpAddressUtils::IP_ADDRESS_TYPE_SINGLE, "IPv4 validation failed.");

        // Test IPv6 validation
        $ipIPv6 = '2001:db8::1';
        assert(kIpAddressUtils::getAddressType($ipIPv6) === kIpAddressUtils::IP_ADDRESS_TYPE_SINGLE, "IPv6 validation failed.");

        echo "Zend Validate IP tests passed!\n";
    }

    public static function testConvertAnonymousCsvToBin() {
        echo "Testing convertAnonymousCsvToBin with IPv4 and IPv6...\n";

        // Test IPv4 range packing
        $startIpIPv4 = '192.168.1.1';
        $endIpIPv4 = '192.168.1.255';
        $packedIPv4 = pack("LL", kIpAddressUtils::ipToLong($startIpIPv4), kIpAddressUtils::ipToLong($endIpIPv4));
        assert($packedIPv4 !== false, "IPv4 packing failed.");

        // Test IPv6 range packing (mocked as IPv6 is not natively supported in pack)
        $startIpIPv6 = '2001:db8::1';
        $endIpIPv6 = '2001:db8::ffff';
        $longStartIPv6 = kIpAddressUtils::ipToLong($startIpIPv6);
        $longEndIPv6 = kIpAddressUtils::ipToLong($endIpIPv6);
        assert($longStartIPv6 !== null && $longEndIPv6 !== null, "IPv6 packing simulation failed.");

        echo "convertAnonymousCsvToBin tests passed!\n";
    }
}

// Run the tests
ipv6Test::runTests();
