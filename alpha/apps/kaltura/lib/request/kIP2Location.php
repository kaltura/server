<?php
/**
 * @package server-infra
 * @subpackage request
 */
include(__DIR__ . "/../../../../../vendor/IP2Location/IP2Location.inc.php");

/**
 * @package server-infra
 * @subpackage request
 */
class kIP2Location
{
	public static function ipToRecord($ip)
	{
		$record = false;
		
		$ip2LocationBinFilePath = __DIR__ . '/../../../../../../data'."/geoip/IP-COUNTRY-ISP.BIN";
		if (function_exists("IP2Location_open") && file_exists($ip2LocationBinFilePath))
		{
			$ip2Location = IP2Location_open($ip2LocationBinFilePath, IP2LOCATION_STANDARD);
			$record = IP2Location_get_all($ip2Location, $ip);
			IP2Location_close($ip2Location);
		}
		
		return $record;
	}
	
	public static function ipToCoordinates($ip)
	{
		$record = self::ipToRecord($ip);
		if ($record)
			return array($record->latitude, $record->longitude);
			
		return false;
	}
	
	public static function ipToCountry($ip)
	{
		$record = self::ipToRecord($ip);
		if($record)
		{
			$result = $record ? $record->country_short : "";
			if ($result == "GB") // retain the old UK country code till all components are adjusted to list GB
				$result = "UK";
	
			return $result;
		}
		
		ini_set('memory_limit', '128M'); // ip_files are large array files, sometimes it might break if doesn't have enough memory
		$country = "";
		$numbers = preg_split( '/\./', $ip);
		$ipFile = __DIR__ . "/../../../../../vendor/IP2Location/ip_files/".$numbers[0].".php";
		
		$included = false;
		$ranges = array();
		if(file_exists($ipFile))
		{
			$included = true;
			include($ipFile);
		}
		if(!$included)
			return "";
			
		$code=($numbers[0] * 16777216) + ($numbers[1] * 65536) + ($numbers[2] * 256) + ($numbers[3]);
		foreach($ranges as $key => $value){
	        if($key<=$code)
		{
			if($ranges[$key][0]>=$code){$country=$ranges[$key][1];break;}
		}
	    }
	    return $country;
	}

	public static function ipToCountryAndCode($ip)
	{
		$country = "";
		$numbers = preg_split( '/\./', $ip);
		$ipFile = __DIR__ . "/../../../../../vendor/IP2Location/ip_files/".$numbers[0].".php";
		
	    $included = false;
		$ranges = array();
		if(file_exists($ipFile))
		{
			$included = true;
			include($ipFile);
		}
		if(!$included)
			return "";
			
		$code=($numbers[0] * 16777216) + ($numbers[1] * 65536) + ($numbers[2] * 256) + ($numbers[3]);
		foreach($ranges as $key => $value)
		{
			if($key<=$code)
			{
				if($ranges[$key][0]>=$code)
				{
					$country=$ranges[$key][1];
					break;
				}
			}
		}
		return array ( $country , $code );
	}
}
