<?php
/** 
 * @package server-infra
 * @subpackage request
 */
class kIpAddressUtils 
{
	const IP_TREE_NODE_VALUE	= 2;
		
	const IP_ADDRESS_TYPE_SINGLE       = 1; // example: 192.168.1.1
	const IP_ADDRESS_TYPE_MASK_ADDRESS = 2; // example: 192.168.1.1/255.255.0.0
	const IP_ADDRESS_TYPE_MASK_CIDR    = 3; // example: 192.168.1.1/24
	const IP_ADDRESS_TYPE_RANGE        = 4; // example: 192.168.1.0-192.168.1.240
	
	const IP_ADDRESS_RANGE_CHAR    = '-';
	const IP_ADDRESS_MASK_CHAR     = '/';
	const IP_ADDRESS_PARTS_DELIMETER = '.';
	
	static protected $isInternalIp = array();
	
	public static function getAddressType($ip)
	{
		$mask  = strpos($ip, self::IP_ADDRESS_MASK_CHAR);
		$range = strpos($ip, self::IP_ADDRESS_RANGE_CHAR);

		if ($mask !== false && $range === false) 
		{ 
			$subNet = trim(substr($ip, $mask + 1));
			if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
				return self::IP_ADDRESS_TYPE_MASK_CIDR; // IPv6 always uses CIDR
			}
			$isNetAddr = strpos($subNet, self::IP_ADDRESS_PARTS_DELIMETER);
			$type = $isNetAddr !== false ? self::IP_ADDRESS_TYPE_MASK_ADDRESS : self::IP_ADDRESS_TYPE_MASK_CIDR;
			return $type;
		} 

		if ($range !== false && $mask === false) 
		{ 
			return self::IP_ADDRESS_TYPE_RANGE; 
		} 

		if (filter_var($ip, FILTER_VALIDATE_IP)) 
		{ 
			return self::IP_ADDRESS_TYPE_SINGLE; 
		} 

		return null;
	}
	
	
	public static function isIpInRange($ip, $range)
	{
		$ip = !is_null($ip) ? trim($ip) : '';
		$range = trim($range);

		$rangeType = self::getAddressType($range);
		if (!$rangeType) {
			if (class_exists('KalturaLog'))
				KalturaLog::err("Cannot identify ip address type for [$range]");
			return false;
		}

		switch ($rangeType)
		{
			case self::IP_ADDRESS_TYPE_SINGLE:
				return self::compareIp($ip, $range) === 0;

			case self::IP_ADDRESS_TYPE_RANGE:
				$d = strpos($range, self::IP_ADDRESS_RANGE_CHAR);
				$fromIp = trim(substr($range, 0, $d));
				$toIp = trim(substr($range, $d + 1));
				return (self::compareIp($ip, $fromIp) >= 0 && self::compareIp($ip, $toIp) <= 0);

			case self::IP_ADDRESS_TYPE_MASK_ADDRESS:
				// Only valid for IPv4
				if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) return false;
				list($rangeIp, $rangeMask) = array_map('trim', explode(self::IP_ADDRESS_MASK_CHAR, $range));
				$ipLong = self::ipToLong($ip);
				$rangeIpLong = self::ipToLong($rangeIp);
				$maskLong = self::ipToLong($rangeMask);
				if ($ipLong === false || $rangeIpLong === false || $maskLong === false) return false;
				return (($ipLong & $maskLong) === ($rangeIpLong & $maskLong));

			case self::IP_ADDRESS_TYPE_MASK_CIDR:
				list($rangeIp, $rangeMask) = array_map('trim', explode(self::IP_ADDRESS_MASK_CHAR, $range));
				$ipBin = self::inetToBits($ip);
				$rangeIpBin = self::inetToBits($rangeIp);
				if ($ipBin === null || $rangeIpBin === null) return false;

				$isIPv6 = (strlen($ipBin) === 128);
				$bits = $isIPv6 ? 128 : 32;
				$rangeMask = (int)$rangeMask;
				if ($rangeMask <= 0 || $rangeMask > $bits) return false;

				$ipMasked = substr($ipBin, 0, $rangeMask);
				$rangeMasked = substr($rangeIpBin, 0, $rangeMask);

				return $ipMasked === $rangeMasked;
		}

		if (class_exists('KalturaLog'))
			KalturaLog::err("IP address type [$rangeType] for [$range] is missing implementation");
		return false;
	}
	
	public static function isIpInRanges($ip, $ranges)
	{	
		foreach (explode(',', $ranges) as $range)
		{
			if(self::isIpInRange($ip, $range))
			{
				return true;
			}
		}
		return false;
	}
	
	
	public static function isInternalIp($ipAddress = null)
	{
		if (!$ipAddress)
		{
			$ipAddress = infraRequestUtils::getRemoteAddress();
		}
		
		if (isset(self::$isInternalIp[$ipAddress]))
			return self::$isInternalIp[$ipAddress];
		
		if (kConf::hasParam('internal_ip_range'))
		{
			$range = kConf::get('internal_ip_range');

			if(self::isIpInRanges($ipAddress, $range))
			{
				self::$isInternalIp[$ipAddress] = true;
				return true;
			}
		}
		self::$isInternalIp[$ipAddress] = false;
		return false;				
	}

	public static function ipRangeToCIDR($from, $to)
	{
		$result = array();
		if (!$from) {
			$from = 1;
		}
		
		while($to >= $from) {
			$netmask = 32 - log((-$from & $from), 2);

			$highBit = log($to - $from + 1, 2);
			$maxDiff = 32 - floor($highBit);

			if($netmask < $maxDiff) {
				$netmask = $maxDiff;
			}

			$result[$from] = $netmask;
			$from += 1 << (32 - $netmask);
		}

		return $result;
	}
	
	public static function compressIpRanges($ipRanges)
	{
		ksort($ipRanges);
		
		$lastRangeTo = -2;
		$compressedIps = array();
		foreach($ipRanges as $ipFrom => $ipTo)
		{
			// either create a new ip range
			if ($ipFrom > $lastRangeTo + 1) {
				$lastRangeFrom = $ipFrom;
				$lastRangeTo = $ipTo;
				$compressedIps[$ipFrom] = $ipTo;
			}
			else if ($ipTo > $lastRangeTo) { // or extend the current range
				$lastRangeTo = $ipTo;
				$compressedIps[$lastRangeFrom] = $ipTo;
			}
		}
	
		return $compressedIps;
	}
	
	public static function parseIpRange($range)
	{
		$rangeType = self::getAddressType($range);

		switch ($rangeType)
		{
			case self::IP_ADDRESS_TYPE_SINGLE:
				$fromIp = $toIp = self::ipToLong($range);
				break;

			case self::IP_ADDRESS_TYPE_RANGE:
				$d = strpos($range, self::IP_ADDRESS_RANGE_CHAR);
				$fromIp = self::ipToLong(substr($range, 0, $d));
				$toIp = self::ipToLong(substr($range, $d + 1));
				break;

			case self::IP_ADDRESS_TYPE_MASK_ADDRESS:
				list (, $rangeMask) = array_map('trim', explode(self::IP_ADDRESS_MASK_CHAR, $range));
				$fromIp = self::ipToLong($rangeMask);
				$base = ~((1 << (32 - (32 - log(($fromIp ^ 0xFFFFFFFF) + 1, 2)))) - 1);
				$toIp = $fromIp + (1 << (32 - log(($fromIp ^ $base) + 1, 2))) - 1;
				break;

			case self::IP_ADDRESS_TYPE_MASK_CIDR:
				list($rangeIp, $rangeMask) = array_map('trim', explode(self::IP_ADDRESS_MASK_CHAR, $range));
				$fromIp = self::ipToLong($rangeIp);
				$isIPv6 = filter_var($rangeIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
				$bits = $isIPv6 ? 128 : 32;
				if ($isIPv6) {
					// Use GMP for 128-bit math
					if (function_exists('gmp_init')) {
						$fromGmp = gmp_init($fromIp);
						$add = gmp_sub(gmp_pow(2, $bits - $rangeMask), 1);
						$toIp = gmp_strval(gmp_add($fromGmp, $add));
					} elseif (extension_loaded('bcmath')) {
						$add = bcsub(bcpow('2', (string)($bits - $rangeMask)), '1');
						$toIp = bcadd($fromIp, $add);
					} else {
						// Fallback: not supported
						$toIp = $fromIp;
					}
				} else {
					$toIp = $fromIp + (1 << ($bits - $rangeMask)) - 1;
				}
				break;
		}
		
		return array($fromIp, $toIp);
	}
	
	/*
	 * insert ip ranges array into an ip binary tree built from the ip bits.
	 * The end node will be set with the given value
	 */
	public static function insertRangesIntoIpTree(&$ipTree, $rangesArr, $value)
	{
		// run over ips and create an array of ip ranges
		$ips = array();

		foreach($rangesArr as $rangesItem)
		{
			$ranges = explode(',', $rangesItem);

			foreach($ranges as $range)
			{
				list($fromIp, $toIp) = self::parseIpRange($range);

				if (!array_key_exists($fromIp, $ips)) {
					$ips[$fromIp] = $toIp;
				}
				else if ($ips[$fromIp] < $toIp) {
					$ips[$fromIp] = $toIp;
				}
			}
		}

		// compress ip ranges 
		$ips = self::compressIpRanges($ips);

		// turn each ip range into a CIDR and add to the ipTree
		foreach($ips as $fromIp => $toIp)
		{
			$rangeCIDRs = self::ipRangeToCIDR($fromIp, $toIp);

			foreach($rangeCIDRs as $rangeIp => $rangeMask)
			{
				$root = &$ipTree;

				for ($bitIndex = 31; $bitIndex >= 32 - $rangeMask; $bitIndex--)
				{
					$bit = ($rangeIp >> $bitIndex) & 1;

					if (!isset($root[$bit])) {
						$root[$bit] = array();
					}

					$root = &$root[$bit];
				}

				if (!isset($root[self::IP_TREE_NODE_VALUE])) {
						$root[self::IP_TREE_NODE_VALUE] = $value;
				} else {
						$root[self::IP_TREE_NODE_VALUE] .= ",$value";
				}
			}
		}
	}

	/**
	 * Traverse an ipTree and return all of the values on the path of a given IP
	 * 
	 * @param string $ip
	 * @param array $ipTree
	 * 
	 * @return array
	 */
	public static function traverseIpTree($ip, $ipTree)
	{
		$values = array();
		if(is_null($ip))
		{
			return $values;
		}
		
		$ipLong = self::ipToLong($ip);
		$root = $ipTree;
		$bitIndex = 32;
		while(1)
		{
			if (isset($root[self::IP_TREE_NODE_VALUE])) {
				$values[] = $root[self::IP_TREE_NODE_VALUE];
			}
				
			if (!$bitIndex) {
				break;
			}
			
			$bitIndex--;
						
			$bit = ($ipLong >> $bitIndex) & 1;
			if (!isset($root[$bit])) {
				break;
			}

			$root = $root[$bit];
		}

		return $values;
	}

	// Add helper methods for IPv6 support
	private static function inetToBits($ip)
	{
		$packed = inet_pton($ip);
		$bits = '';
		foreach (str_split($packed) as $char) {
			$bits .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
		}
		return $bits;
	}

    public static function ipToLong($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return (string)ip2long($ip);
        } elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $bin = inet_pton($ip);
            if (function_exists('gmp_import')) {
                $gmp = gmp_import($bin);
                return gmp_strval($gmp);
            } elseif (extension_loaded('gmp')) {
                $hex = bin2hex($bin);
                $gmp = gmp_init($hex, 16);
                return gmp_strval($gmp);
            } elseif (extension_loaded('bcmath')) {
                $hex = bin2hex($bin);
                $dec = '0';
                for ($i = 0; $i < strlen($hex); $i += 4) {
                    $chunk = substr($hex, $i, 4);
                    $dec = bcmul($dec, bcpow('16', strlen($chunk)));
                    $dec = bcadd($dec, hexdec($chunk));
                }
                return $dec;
            } else {
                // Fallback: return as hex string
                return bin2hex($bin);
            }
        }
        return false; // Invalid IP
    }

    private static function compareIp($ip1, $ip2)
	{
		$bin1 = self::inetToBits($ip1);
		$bin2 = self::inetToBits($ip2);
		// Use GMP for IPv6, string compare for IPv4
		if (filter_var($ip1, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) || filter_var($ip2, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			$long1 = self::ipToLong($ip1);
			$long2 = self::ipToLong($ip2);
			if (function_exists('gmp_cmp')) {
				return gmp_cmp(gmp_init($long1), gmp_init($long2));
			} elseif (extension_loaded('bcmath')) {
				return bccomp($long1, $long2);
			} else {
				return strcmp($long1, $long2);
			}
		}
		return strcmp($bin1, $bin2);
	}
}
