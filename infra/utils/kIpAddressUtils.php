<?php
/** 
 * @package infra
 * @subpackage utils
 */
class kIpAddressUtils 
{
	
	const IP_ADDRESS_TYPE_SINGLE       = 1; // example: 192.168.1.1
	const IP_ADDRESS_TYPE_MASK_ADDRESS = 2; // example: 192.168.1.1/255.255.0.0
	const IP_ADDRESS_TYPE_MASK_CIDR    = 3; // example: 192.168.1.1/24
	const IP_ADDRESS_TYPE_RANGE        = 4; // example: 192.168.1.0-192.168.1.240
	
	const IP_ADDRESS_RANGE_CHAR    = '-';
	const IP_ADDRESS_MASK_CHAR     = '/';
	const IP_ADDRESS_PARTS_DELIMETER = '.';
	
	
	private static function getAddressType($ip)
	{
		$mask     = strpos($ip, self::IP_ADDRESS_MASK_CHAR);
		$range    = strpos($ip, self::IP_ADDRESS_RANGE_CHAR);
		
        if ($mask && !$range) 
        { 
        	$subNet = trim(substr($ip,$mask+1));
        	$isNetAddr = strpos($subNet, self::IP_ADDRESS_PARTS_DELIMETER);
        	$type = $isNetAddr ? self::IP_ADDRESS_TYPE_MASK_ADDRESS : self::IP_ADDRESS_TYPE_MASK_CIDR;
        	return $type;
        } 

        if ($range && !$mask) 
        { 
            return self::IP_ADDRESS_TYPE_RANGE; 
        } 

        if (ip2long($ip) && !$range && !$mask) 
        { 
            return self::IP_ADDRESS_TYPE_SINGLE; 
        } 
        
        return null;
	}
	
	
	public static function isIpInRange($ip, $range)
	{
		$ip = trim($ip);
		$range = trim($range);
		
		$rangeType = self::getAddressType($range);
		if (!$rangeType) {
			KalturaLog::err("Cannot identify ip address type for [$range]");
			return false;
		}
		
		switch ($rangeType)
		{
			case self::IP_ADDRESS_TYPE_SINGLE:
				return ip2long($ip) === ip2long($range);

			case self::IP_ADDRESS_TYPE_RANGE:
				$d = strpos($range, self::IP_ADDRESS_RANGE_CHAR);
				$fromIp = trim(ip2long(substr($range,0,$d)));
       			$toIp = trim(ip2long(substr($range,$d+1)));
       			$ip = ip2long($ip);
				return ($ip>=$fromIp && $ip<=$toIp);

			case self::IP_ADDRESS_TYPE_MASK_ADDRESS:
				list ($rangeIp, $rangeMask) = array_map('trim', explode(self::IP_ADDRESS_MASK_CHAR, $range));
		        $fromIp = (ip2long($rangeIp) &ip2long($rangeMask)) + 1; 
		        $toIp = (ip2long($rangeIp) | (~ip2long($rangeMask))) + 1; 
		        $ip = ip2long($ip); 
		        return ($ip >= $fromIp && $ip <= $toIp);
				
			case self::IP_ADDRESS_TYPE_MASK_CIDR:
				list ($rangeIp, $rangeMask) = array_map('trim', explode(self::IP_ADDRESS_MASK_CHAR, $range));
    			if ($rangeMask <= 0){
    				return false;
    			} 
       			$ipBinaryStr = sprintf("%032b",ip2long($ip)); 
       			$netBinaryStr = sprintf("%032b",ip2long($rangeIp)); 
        		return (substr_compare($ipBinaryStr,$netBinaryStr,0,$rangeMask) === 0);
		}
		
		KalturaLog::err("IP address type [$rangeType] for [$range] is missing implementation");
		return false;		
	}
	
	
	
}