<?php
@include("IP2Location.inc.php");

class myIPGeocoder
{

	function iptocountry($ip) 
	{   
		$ip2LocationBinFilePath = SF_ROOT_DIR.'/../../data'."/geoip/IP-COUNTRY-ISP.BIN";
		if (function_exists("IP2Location_open") && file_exists($ip2LocationBinFilePath))
		{
			$ip2Location = IP2Location_open($ip2LocationBinFilePath, IP2LOCATION_STANDARD);
			$record = IP2Location_get_country_short($ip2Location, $ip);
			IP2Location_close($ip2Location);
			return $record ? $record->country_short : "";
		}
		
		ini_set('memory_limit', '128M'); // ip_files are large array files, sometimes it might break if doesn't have enough memory
		$country = "";
		$numbers = preg_split( "/\./", $ip);   
		include("ip_files/".$numbers[0].".php");
		$code=($numbers[0] * 16777216) + ($numbers[1] * 65536) + ($numbers[2] * 256) + ($numbers[3]);   
		foreach($ranges as $key => $value){
	        if($key<=$code){
	            if($ranges[$key][0]>=$code){$country=$ranges[$key][1];break;}
	            }
	    }
	    return $country;
	}
	
	function iptocountryAndCode($ip) 
	{   
		$country = "";
	    $numbers = preg_split( "/\./", $ip);   
	    include("ip_files/".$numbers[0].".php");
	    $code=($numbers[0] * 16777216) + ($numbers[1] * 65536) + ($numbers[2] * 256) + ($numbers[3]);   
	    foreach($ranges as $key => $value){
	        if($key<=$code){
	            if($ranges[$key][0]>=$code){$country=$ranges[$key][1];break;}
	            }
	    }
	    return array ( $country , $code );
	}	
}
?>