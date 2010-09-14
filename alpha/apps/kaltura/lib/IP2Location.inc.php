<?php
/* IP2Location.inc
 *
 * Copyright (C) 2005-2008 IP2Location.com
 *
 * http://www.ip2location.com
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General var
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General var License for more details.
 *
 * You should have received a copy of the GNU Lesser General var
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

define("VERSION", "3.00");

define("UNKNOWN", "UNKNOWN IP ADDRESS");
define("NO_IP", "MISSING IP ADDRESS");
define("FILEHANDLE_NULL", "Unable to open binary input file ");
define("INVALID_IPV6_ADDRESS", "INVALID IPV6 ADDRESS");
define("INVALID_IPV4_ADDRESS", "INVALID IPV4 ADDRESS");
define("NOT_SUPPORTED", "This parameter is unavailable for selected data file. Please upgrade the data file.");
define("MAX_IPV4_RANGE", 4294967295);
define("MAX_IPV6_RANGE", "340282366920938463463374607431768211455");
define("IP_COUNTRY", 1);
define("IP_COUNTRY_ISP", 2);
define("IP_COUNTRY_REGION_CITY", 3);
define("IP_COUNTRY_REGION_CITY_ISP", 4);
define("IP_COUNTRY_REGION_CITY_LATITUDE_LONGITUDE", 5);
define("IP_COUNTRY_REGION_CITY_LATITUDE_LONGITUDE_ISP", 6);
define("IP_COUNTRY_REGION_CITY_ISP_DOMAIN", 7);
define("IP_COUNTRY_REGION_CITY_LATITUDE_LONGITUDE_ISP_DOMAIN", 8);
define("IP_COUNTRY_REGION_CITY_LATITUDE_LONGITUDE_ZIPCODE", 9);
define("IP_COUNTRY_REGION_CITY_LATITUDE_LONGITUDE_ZIPCODE_ISP_DOMAIN", 10);
define("IP_COUNTRY_REGION_CITY_LATITUDE_LONGITUDE_ZIPCODE_TIMEZONE", 11);
define("IP_COUNTRY_REGION_CITY_LATITUDE_LONGITUDE_ZIPCODE_TIMEZONE_ISP_DOMAIN", 12);
define("IP_COUNTRY_REGION_CITY_LATITUDE_LONGITUDE_TIMEZONE_NETSPEED", 13);
define("IP_COUNTRY_REGION_CITY_LATITUDE_LONGITUDE_ZIPCODE_TIMEZONE_ISP_DOMAIN_NETSPEED", 14);
define("IP_COUNTRY_REGION_CITY_LATITUDE_LONGITUDE_ZIPCODE_TIMEZONE_AREACODE", 15);
define("IP_COUNTRY_REGION_CITY_LATITUDE_LONGITUDE_ZIPCODE_TIMEZONE_ISP_DOMAIN_NETSPEED_AREACODE", 16);
define("IP_COUNTRY_REGION_CITY_LATITUDE_LONGITUDE_TIMEZONE_NETSPEED_WEATHER", 17);
define("IP_COUNTRY_REGION_CITY_LATITUDE_LONGITUDE_ZIPCODE_TIMEZONE_ISP_DOMAIN_NETSPEED_AREACODE_WEATHER", 18);
define("COUNTRYSHORT", 1);
define("COUNTRYLONG", 2);
define("REGION", 3);
define("CITY", 4);
define("ISP", 5);
define("LATITUDE", 6);
define("LONGITUDE", 7);
define("DOMAIN", 8);
define("ZIPCODE", 9);
define("TIMEZONE", 10);
define("NETSPEED", 11);
define("IDDCODE", 12);
define("AREACODE", 13);
define("WEATHERCODE", 14);
define("WEATHERNAME", 15);

define("ALL", 100);
define("IP2LOCATION_STANDARD", 20);
define("IPV4", 0);
define("IPV6", 1);

function big_endian_unpack ($format, $data) {
   $ar = unpack ($format, $data);
   $vals = array_values ($ar);
   $f = explode ('/', $format);
   $i = 0;
   foreach ($f as $f_k => $f_v) {
   $repeater = intval (substr ($f_v, 1));
   if ($repeater == 0) $repeater = 1;
   if ($f_v{1} == '*')
   {
       $repeater = count ($ar) - $i;
   }
   if ($f_v{0} != 'd') { $i += $repeater; continue; }
   $j = $i + $repeater;
   for ($a = $i; $a < $j; ++$a)
   {
       $p = pack ('d',$vals[$i]);
       $p = strrev ($p);
       list ($vals[$i]) = array_values (unpack ('d1d', $p));
       ++$i;
   }
   }
   $a = 0;
   foreach ($ar as $ar_k => $ar_v) {
   $ar[$ar_k] = $vals[$a];
   ++$a;
   }
   return $ar;
}

list ($endiantest) = array_values (unpack ('L1L', pack ('V',1)));
if ($endiantest != 1)
	define ('BIG_ENDIAN_MACHINE',1);
if (defined ('BIG_ENDIAN_MACHINE'))
	$GLOBALS['unpack_workaround'] = 'big_endian_unpack';
else
	$GLOBALS['unpack_workaround'] = 'unpack';

class IP2Location {
	var $flags;
	var $filehandle;
	var $databasetype;
	var $databasecolumn;
	var $databaseyear;
	var $databasemonth;
	var $databaseday;
	var $databasecount;
	var $databaseaddr;
}

class IP2LocationRecord {
  var $country_short;
  var $country_long;
  var $region;
  var $city;
  var $isp;
  var $latitude;
  var $longitude;
  var $domain;
  var $zipcode;
  var $timezone;
  var $netspeed;
  var $idd_code;
  var $area_code;
  var $weather_code;
  var $weather_name;
  var $ipaddr;
  var $ipno;
}

function IP2Location_open ($filename, $flags) {
	$ip = new IP2Location;
	$ip->flags = $flags;
	$ip->filehandle = fopen($filename,"rb");
	if ($ip->filehandle === false) {
		echo FILEHANDLE_NULL . $filename . ".\n";;
		return 0;
	}
	$ip = IP2Location_initialize($ip);
	return $ip;
}

function IP2Location_close ($ip) {
	if ($ip->filehandle == NULL) {
		return NULL;
	}
  return fclose($ip->filehandle);
}

function IP2Location_initialize ($ip) {
	$ip->databasetype = IP2Location_read8($ip, 1);
	$ip->databasecolumn = IP2Location_read8($ip, 2);
	$ip->databaseyear = IP2Location_read8($ip, 3);
	$ip->databasemonth = IP2Location_read8($ip, 4);
	$ip->databaseday = IP2Location_read8($ip, 5);
	$ip->databasecount = IP2Location_read32($ip, 6);
	$ip->databaseaddr = IP2Location_read32($ip, 10);
	$ip->ipversion = IP2Location_read32($ip, 14);
	return $ip;
}

function IP2Location_get_module_version () {
	return VERSION;
}

function IP2Location_get_country_short ($ip, $ipaddr) {
	if ($ip->{"ipversion"} == IPV6) {
		return IP2Location_get_ipv6_record($ip, $ipaddr, COUNTRYSHORT);
	} else {
		return IP2Location_get_record($ip, $ipaddr, COUNTRYSHORT);
	}

}

function IP2Location_get_country_long ($ip, $ipaddr) {
	if ($ip->{"ipversion"} == IPV6) {
		return IP2Location_get_ipv6_record($ip, $ipaddr, COUNTRYLONG);
	} else {
		return IP2Location_get_record($ip, $ipaddr, COUNTRYLONG);
	}

}

function IP2Location_get_region ($ip, $ipaddr) {
	if ($ip->{"ipversion"} == IPV6) {
		return IP2Location_get_ipv6_record($ip, $ipaddr, REGION);
	} else {
		return IP2Location_get_record($ip, $ipaddr, REGION);
	}

}

function IP2Location_get_city ($ip, $ipaddr) {
	if ($ip->{"ipversion"} == IPV6) {
		return IP2Location_get_ipv6_record($ip, $ipaddr, CITY);
	} else {
		return IP2Location_get_record($ip, $ipaddr, CITY);
	}

}

function IP2Location_get_isp ($ip, $ipaddr) {
	if ($ip->{"ipversion"} == IPV6) {
		return IP2Location_get_ipv6_record($ip, $ipaddr, ISP);
	} else {
		return IP2Location_get_record($ip, $ipaddr, ISP);
	}

}

function IP2Location_get_latitude ($ip, $ipaddr) {
	if ($ip->{"ipversion"} == IPV6) {
		return IP2Location_get_ipv6_record($ip, $ipaddr, LATITUDE);
	} else {
		return IP2Location_get_record($ip, $ipaddr, LATITUDE);
	}

}

function IP2Location_get_longitude ($ip, $ipaddr) {
	if ($ip->{"ipversion"} == IPV6) {
		return IP2Location_get_ipv6_record($ip, $ipaddr, LONGITUDE);
	} else {
		return IP2Location_get_record($ip, $ipaddr, LONGITUDE);
	}

}

function IP2Location_get_zipcode ($ip, $ipaddr) {
	if ($ip->{"ipversion"} == IPV6) {
		return IP2Location_get_ipv6_record($ip, $ipaddr, ZIPCODE);
	} else {
		return IP2Location_get_record($ip, $ipaddr, ZIPCODE);
	}

}

function IP2Location_get_domain ($ip, $ipaddr) {
	if ($ip->{"ipversion"} == IPV6) {
		return IP2Location_get_ipv6_record($ip, $ipaddr, DOMAIN);
	} else {
		return IP2Location_get_record($ip, $ipaddr, DOMAIN);
	}

}

function IP2Location_get_timezone ($ip, $ipaddr) {
	if ($ip->{"ipversion"} == IPV6) {
		return IP2Location_get_ipv6_record($ip, $ipaddr, TIMZONE);
	} else {
		return IP2Location_get_record($ip, $ipaddr, TIMEZONE);
	}

}

function IP2Location_get_netspeed ($ip, $ipaddr) {
	if ($ip->{"ipversion"} == IPV6) {
		return IP2Location_get_ipv6_record($ip, $ipaddr, NETSPEED);
	} else {
		return IP2Location_get_record($ip, $ipaddr, NETSPEED);
	}

}

function IP2Location_get_idd_code ($ip, $ipaddr) {
	if ($ip->{"ipversion"} == IPV6) {
		return IP2Location_get_ipv6_record($ip, $ipaddr, IDDCODE);
	} else {
		return IP2Location_get_record($ip, $ipaddr, IDDCODE);
	}

}

function IP2Location_get_area_code ($ip, $ipaddr) {
	if ($ip->{"ipversion"} == IPV6) {
		return IP2Location_get_ipv6_record($ip, $ipaddr, AREACODE);
	} else {
		return IP2Location_get_record($ip, $ipaddr, AREACODE);
	}

}

function IP2Location_get_weather_code ($ip, $ipaddr) {
	if ($ip->{"ipversion"} == IPV6) {
		return IP2Location_get_ipv6_record($ip, $ipaddr, WEATHERCODE);
	} else {
		return IP2Location_get_record($ip, $ipaddr, WEATHERCODE);
	}

}

function IP2Location_get_weather_name ($ip, $ipaddr) {
	if ($ip->{"ipversion"} == IPV6) {
		return IP2Location_get_ipv6_record($ip, $ipaddr, WEATHERNAME);
	} else {
		return IP2Location_get_record($ip, $ipaddr, WEATHERNAME);
	}

}

function IP2Location_read8 ($ip, $pos) {
	global $unpack_workaround;
	if ($ip->filehandle == NULL) {
		return NULL;
	}
	fseek($ip->filehandle, $pos-1, SEEK_SET);
  $data = fread($ip->filehandle, 1);
	$adata = $unpack_workaround("C", $data);
  return $adata[1];
}

function IP2Location_read32 ($ip, $pos) {
	global $unpack_workaround;
	if ($ip->filehandle == NULL) {
		return NULL;
	}
	fseek($ip->filehandle, $pos-1, SEEK_SET);
  $data = fread($ip->filehandle, 4);
  $adata = $unpack_workaround('V', $data);
  if ($adata[1] < 0) {
  	$adata[1] += 4294967296;
  }
  return (int)$adata[1];
}

function IP2Location_read128 ($ip, $pos) {
  global $unpack_workaround;
	if ($ip->filehandle == NULL) {
		return NULL;
	}
	fseek($ip->filehandle, $pos-1, SEEK_SET);
  $data = fread($ip->filehandle, 16);
  return IP2Location_bytes2int($data);
}

function IP2Location_readStr ($ip, $pos) {
	global $unpack_workaround;
	if ($ip->filehandle == NULL) {
		return NULL;
	}
	fseek($ip->filehandle, $pos, SEEK_SET);
	$size = fread($ip->filehandle, 1);
	$adata = $unpack_workaround("C", $size);
	$data = fread($ip->filehandle, $adata[1]);
	return $data;
}

function IP2Location_readFloat ($ip, $pos) {
	global $unpack_workaround;
	if ($ip->filehandle == NULL) {
		return NULL;
	}
	fseek($ip->filehandle, $pos-1, SEEK_SET);
  $data = fread($ip->filehandle, 4);
  $adata = $unpack_workaround("f", $data);
  return $adata[1];
}

function IP2Location_bytes2int ($bindata) {
	global $unpack_workaround;
	$array = preg_split('//', $bindata, -1, PREG_SPLIT_NO_EMPTY);
	if(count($array) != 16) {
		return 0;
	}

	$ip96_127 = $unpack_workaround("V", $array[0] . $array[1] . $array[2] . $array[3]);
	$ip64_95 = $unpack_workaround("V", $array[4] . $array[5] . $array[6] . $array[7]);
	$ip32_63 = $unpack_workaround("V", $array[8] . $array[9] . $array[10] . $array[11]);
	$ip1_31 = $unpack_workaround("V", $array[12] . $array[13] . $array[14] . $array[15]);
	if ($ip96_127[1] < 0) {
  	$ip96_127[1] += 4294967296;
  }

  if ($ip64_95[1] < 0) {
  	$ip64_95[1] += 4294967296;
  }

	if ($ip32_63[1] < 0) {
  	$ip32_63[1] += 4294967296;
  }

  if ($ip1_31[1] < 0) {
  	$ip1_31[1] += 4294967296;
  }

	$bcresult =  bcadd( bcadd( bcmul($ip1_31[1],  bcpow(4294967296, 3)), bcmul($ip32_63[1],  bcpow(4294967296, 2))),
	  								  bcadd( bcmul($ip64_95[1],  4294967296),$ip96_127[1]));
	return $bcresult;

}

function IP2Location_ip2no ($ipaddr) {
	global $unpack_workaround;
	$long_ip = ip2long($ipaddr);
	if ($long_ip < 0) {
		$long_ip += pow(2,32);
	}
	return $long_ip;
}

function IP2Location_name2ip ($name) {
	return gethostbyname($name);
}

function IP2Location_get_ipv6_record ($ip, $ipaddr, $mode) {
	$IPV6_COUNTRY_POSITION =   array(0, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2);
	$IPV6_REGION_POSITION =    array(0, 0, 0, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3);
	$IPV6_CITY_POSITION =      array(0, 0, 0, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4);
	$IPV6_ISP_POSITION =       array(0, 0, 3, 0, 5, 0, 7, 5, 7, 0, 8, 0, 9, 0, 9);
	$IPV6_LATITUDE_POSITION =  array(0, 0, 0, 0, 0, 5, 5, 0, 5, 5, 5, 5, 5, 5, 5);
	$IPV6_LONGITUDE_POSITION = array(0, 0, 0, 0, 0, 6, 6, 0, 6, 6, 6, 6, 6, 6, 6);
	$IPV6_DOMAIN_POSITION =    array(0, 0, 0, 0, 0, 0, 0, 6, 8, 0, 9, 0, 10,0, 10);
	$IPV6_ZIPCODE_POSITION =   array(0, 0, 0, 0, 0, 0, 0, 0, 0, 7, 7, 7, 7, 0, 7);
	$IPV6_TIMEZONE_POSITION =  array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 8, 8, 7, 8);
	$IPV6_NETSPEED_POSITION =  array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 8, 11);
	$dbtype = $ip->databasetype;

	if ($ipaddr == "") {
		return NO_IP;
	}

	$record = new IP2LocationRecord;

	if (!IP2Location_ip_is_ipv6($ipaddr)) {
		$record->country_short = INVALID_IPV6_ADDRESS;
		$record->country_long = INVALID_IPV6_ADDRESS;
		$record->region = INVALID_IPV6_ADDRESS;
		$record->city = INVALID_IPV6_ADDRESS;
		$record->isp = INVALID_IPV6_ADDRESS;
		$record->latitude = INVALID_IPV6_ADDRESS;
		$record->longitude = INVALID_IPV6_ADDRESS;
		$record->domain = INVALID_IPV6_ADDRESS;
		$record->zipcode = INVALID_IPV6_ADDRESS;
		$record->timezone = INVALID_IPV6_ADDRESS;
		$record->netspeed = INVALID_IPV6_ADDRESS;
		$record->idd_code = INVALID_IPV6_ADDRESS;
		$record->area_code = INVALID_IPV6_ADDRESS;
		$record->weather_code = INVALID_IPV6_ADDRESS;
		$record->weather_name = INVALID_IPV6_ADDRESS;
		$record->ipaddr = INVALID_IPV6_ADDRESS;
		$record->ipno = INVALID_IPV6_ADDRESS;
		return $record;
	}

	if (($mode == COUNTRYSHORT) && ($IPV6_COUNTRY_POSITION[$dbtype] == 0)) {
		$record->country_short = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == COUNTRYLONG) && ($IPV6_COUNTRY_POSITION[$dbtype] == 0)) {
		$record->country_long = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == REGION) && ($IPV6_REGION_POSITION[$dbtype] == 0)) {
		$record->region = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == CITY) && ($IPV6_CITY_POSITION[$dbtype] == 0)) {
		$record->city = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == ISP) && ($IPV6_ISP_POSITION[$dbtype] == 0)) {
		$record->isp = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == LATITUDE) && ($LIPV6_ATITUDE_POSITION[$dbtype] == 0)) {
		$record->latitude = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == LONGITUDE) && ($IPV6_LONGITUDE_POSITION[$dbtype] == 0)) {
		$record->longitude = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == DOMAIN) && ($IPV6_DOMAIN_POSITION[$dbtype] == 0)) {
		$record->domain = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == ZIPCODE) && ($IPV6_ZIPCODE_POSITION[$dbtype] == 0)) {
		$record->zipcode = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == TIMEZONE) && ($IPV6_TIMEZONE_POSITION[$dbtype] == 0)) {
		$record->timezone = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == NETSPEED) && ($IPV6_NETSPEED_POSITION[$dbtype] == 0)) {
		$record->netspeed = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == IDDCODE) && ($IPV6_NETSPEED_POSITION[$dbtype] == 0)) {
		$record->idd_code = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == AREACODE) && ($IPV6_NETSPEED_POSITION[$dbtype] == 0)) {
		$record->area_code = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == WEATHERCODE) && ($IPV6_NETSPEED_POSITION[$dbtype] == 0)) {
		$record->weather_code = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == WEATHERNAME) && ($IPV6_NETSPEED_POSITION[$dbtype] == 0)) {
		$record->weather_name = NOT_SUPPORTED;
		return $record;
	}

	$realipno = IP2Location_ipv6_to_no($ipaddr);

	$handle = $ip->filehandle;
	$baseaddr = $ip->databaseaddr;
	$dbcount = $ip->databasecount;
	$dbcolumn = $ip->databasecolumn;

	$low = 0;
	$high = $dbcount;
	$mid = 0;
	$ipfrom = 0;
	$ipto = 0;
	$ipno = 0;

	if (bccomp($realipno, MAX_IPV6_RANGE) == 0) {
		$ipno = bcsub($realipno, 1);
	} else {
		$ipno = $realipno;
	}

	$record->country_short = NOT_SUPPORTED;
	$record->country_long = NOT_SUPPORTED;
	$record->region = NOT_SUPPORTED;
	$record->city = NOT_SUPPORTED;
	$record->isp = NOT_SUPPORTED;
	$record->latitude = NOT_SUPPORTED;
	$record->longitude = NOT_SUPPORTED;
	$record->domain = NOT_SUPPORTED;
	$record->zipcode = NOT_SUPPORTED;
	$record->timezone = NOT_SUPPORTED;
	$record->netspeed = NOT_SUPPORTED;
	$record->idd_code = NOT_SUPPORTED;
	$record->area_code = NOT_SUPPORTED;
	$record->weather_code = NOT_SUPPORTED;
	$record->weather_name = NOT_SUPPORTED;
	$record->ipaddr = $ipaddr;
	$record->ipno = $realipno;

	$count=0;
	while ($low <= $high) {
		$mid = (int)(($low + $high)/2);
		$ipfrom = IP2Location_read128($ip, $baseaddr + $mid * ($dbcolumn * 4 + 12));
		$ipto = IP2Location_read128($ip, $baseaddr + ($mid + 1) * ($dbcolumn * 4 + 12));

		$count++;

		if ((bccomp($ipno, $ipfrom) >= 0 ) and (bccomp($ipno,$ipto) < 0)) {

			if ($mode == COUNTRYSHORT) {
				$record->country_short = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_COUNTRY_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == COUNTRYLONG) {
				$record->country_long = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid * ($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_COUNTRY_POSITION[$dbtype]-1))+3);
				return $record;
			}
			if ($mode == REGION) {
				$record->region = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_REGION_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == CITY) {
				$record->city = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_CITY_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == ISP) {
				$record->isp = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_ISP_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == LATITUDE) {
				$record->latitude = IP2Location_readFloat($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_LATITUDE_POSITION[$dbtype]-1));
				return $record;
			}
			if ($mode == LONGITUDE) {
				$record->longitude = IP2Location_readFloat($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_LONGITUDE_POSITION[$dbtype]-1));
				return $record;
			}
			if ($mode == DOMAIN) {
				$record->domain = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_DOMAIN_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == ZIPCODE) {
				$record->zipcode = IP2Location_readStr($handle, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_ZIPCODE_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == TIMEZONE) {
				$record->timezone = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_TIMEZONE_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == NETSPEED) {
				$record->netspeed = IP2Location_readStr($handle, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_NETSPEED_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == IDDCODE) {
				$record->idd_code = IP2Location_readStr($handle, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_IDDCODE_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == AREACODE) {
				$record->area_code = IP2Location_readStr($handle, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_AREACODE_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == WEATHERCODE) {
				$record->weather_code = IP2Location_readStr($handle, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_WEATHERCODE_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == WEATHERNAME) {
				$record->weather_name = IP2Location_readStr($handle, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_WEATHERNAME_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == ALL) {

				if ($IPV6_COUNTRY_POSITION[$dbtype] != 0) {
					$record->country_short = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_COUNTRY_POSITION[$dbtype]-1)));
					$record->country_long = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_COUNTRY_POSITION[$dbtype]-1))+3);
				}
				if ($IPV6_REGION_POSITION[$dbtype] != 0) {
					$record->region = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_REGION_POSITION[$dbtype]-1)));
				}
				if ($IPV6_CITY_POSITION[$dbtype] != 0) {
					$record->city = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_CITY_POSITION[$dbtype]-1)));
				}
				if ($IPV6_ISP_POSITION[$dbtype] != 0) {
					$record->isp = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_ISP_POSITION[$dbtype]-1)));
				}
				if ($IPV6_LATITUDE_POSITION[$dbtype] != 0) {
					$record->latitude = IP2Location_readFloat($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_LATITUDE_POSITION[$dbtype]-1));
				}
				if ($IPV6_LONGITUDE_POSITION[$dbtype] != 0) {
					$record->longitude = IP2Location_readFloat($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_LONGITUDE_POSITION[$dbtype]-1));
				}
				if ($IPV6_DOMAIN_POSITION[$dbtype] != 0) {
					$record->domain = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_DOMAIN_POSITION[$dbtype]-1)));
				}
				if ($IPV6_ZIPCODE_POSITION[$dbtype] != 0) {
					$record->zipcode = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_ZIPCODE_POSITION[$dbtype]-1)));
				}
				if ($IPV6_TIMEZONE_POSITION[$dbtype] != 0) {
					$record->timezone = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_TIMEZONE_POSITION[$dbtype]-1)));
				}
				if ($IPV6_NETSPEED_POSITION[$dbtype] != 0) {
					$record->netspeed = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_NETSPEED_POSITION[$dbtype]-1)));
				}
				if ($IPV6_IDDCODE_POSITION[$dbtype] != 0) {
					$record->idd_code = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_IDDCODE_POSITION[$dbtype]-1)));
				}
				if ($IPV6_AREACODE_POSITION[$dbtype] != 0) {
					$record->area_code = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_AREACODE_POSITION[$dbtype]-1)));
				}
				if ($IPV6_WEATHERCODE_POSITION[$dbtype] != 0) {
					$record->weather_code = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_WEATHERCODE_POSITION[$dbtype]-1)));
				}
				if ($IPV6_WEATHERNAME_POSITION[$dbtype] != 0) {
					$record->weather_name = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4 + 12) + 12 + 4 * ($IPV6_WEATHERNAME_POSITION[$dbtype]-1)));
				}
				return $record;
			}
		} else {
			if (bccomp($ipno, $ipfrom) < 0) {
				$high = $mid - 1;
			} else {
				$low = $mid + 1;
			}
		}
	}
	return $record;
}


function IP2Location_get_record ($ip, $ipaddr, $mode) {
	$COUNTRY_POSITION		= array(0, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2, 2);
	$REGION_POSITION		= array(0, 0, 0, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3, 3);
	$CITY_POSITION			= array(0, 0, 0, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4, 4);
	$ISP_POSITION			= array(0, 0, 3, 0, 5, 0, 7, 5, 7, 0, 8, 0, 9, 0, 9, 0, 9, 0, 9);
	$LATITUDE_POSITION		= array(0, 0, 0, 0, 0, 5, 5, 0, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5);
	$LONGITUDE_POSITION		= array(0, 0, 0, 0, 0, 6, 6, 0, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6, 6);
	$DOMAIN_POSITION		= array(0, 0, 0, 0, 0, 0, 0, 6, 8, 0, 9, 0, 10,0, 10,0, 10,0, 10);
	$ZIPCODE_POSITION		= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 7, 7, 7, 7, 0, 7, 7, 7, 0, 7);
	$TIMEZONE_POSITION		= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 8, 8, 7, 8, 8, 8, 7, 8);
	$NETSPEED_POSITION		= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 8, 11,0, 11,8, 11);
	$IDDCODE_POSITION		= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 9, 12,0, 12);
	$AREACODE_POSITION		= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10,13,0, 13);
	$WEATHERCODE_POSITION	= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 9, 14);
	$WEATHERNAME_POSITION	= array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 10,15);
	$dbtype = $ip->databasetype;

	if (($mode == COUNTRYSHORT) && ($COUNTRY_POSITION[$dbtype] == 0)) {
		return NOT_SUPPORTED;
	}
	if (($mode == COUNTRYLONG) && ($COUNTRY_POSITION[$dbtype] == 0)) {
		return NOT_SUPPORTED;
	}
	if (($mode == REGION) && ($REGION_POSITION[$dbtype] == 0)) {
		return NOT_SUPPORTED;
	}
	if (($mode == CITY) && ($CITY_POSITION[$dbtype] == 0)) {
		return NOT_SUPPORTED;
	}
	if (($mode == ISP) && ($ISP_POSITION[$dbtype] == 0)) {
		return NOT_SUPPORTED;
	}
	if (($mode == LATITUDE) && ($LATITUDE_POSITION[$dbtype] == 0)) {
		return NOT_SUPPORTED;
	}
	if (($mode == LONGITUDE) && ($LONGITUDE_POSITION[$dbtype] == 0)) {
		return NOT_SUPPORTED;
	}

	if (($mode == DOMAIN) && ($DOMAIN_POSITION[$dbtype] == 0)) {
		return NOT_SUPPORTED;
	}
	if (($mode == ZIPCODE) && ($ZIPCODE_POSITION[$dbtype] == 0)) {
		return NOT_SUPPORTED;
	}
	if (($mode == TIMEZONE) && ($TIMEZONE_POSITION[$dbtype] == 0)) {
		$record->timezone = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == NETSPEED) && ($NETSPEED_POSITION[$dbtype] == 0)) {
		$record->netspeed = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == IDDCODE) && ($IDDCODE_POSITION[$dbtype] == 0)) {
		$record->idd_code = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == AREACODE) && ($AREACODE_POSITION[$dbtype] == 0)) {
		$record->area_code = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == WEATHERCODE) && ($WEATHERCODE_POSITION[$dbtype] == 0)) {
		$record->weather_code = NOT_SUPPORTED;
		return $record;
	}
	if (($mode == WEATHERNAME) && ($WEATHERNAME_POSITION[$dbtype] == 0)) {
		$record->weather_name = NOT_SUPPORTED;
		return $record;
	}

	if ($ipaddr == "") {
		return NO_IP;
	}

	$record = new IP2LocationRecord;

	if (!IP2Location_ip_is_ipv4($ipaddr)) {
		$record->country_short = INVALID_IPV4_ADDRESS;
		$record->country_long = INVALID_IPV4_ADDRESS;
		$record->region = INVALID_IPV4_ADDRESS;
		$record->city = INVALID_IPV4_ADDRESS;
		$record->isp = INVALID_IPV4_ADDRESS;
		$record->latitude = INVALID_IPV4_ADDRESS;
		$record->longitude = INVALID_IPV4_ADDRESS;
		$record->domain = INVALID_IPV4_ADDRESS;
		$record->zipcode = INVALID_IPV4_ADDRESS;
		$record->timezone = NOT_SUPPORTED;
		$record->netspeed = NOT_SUPPORTED;
		$record->idd_code = NOT_SUPPORTED;
		$record->area_code = NOT_SUPPORTED;
		$record->weather_code = NOT_SUPPORTED;
		$record->weather_name = NOT_SUPPORTED;
		$record->ipaddr = INVALID_IPV4_ADDRESS;
		$record->ipno = INVALID_IPV4_ADDRESS;
		return $record;
	}

	$ipaddr = IP2Location_name2ip($ipaddr);
	$realipno = IP2Location_ip2no($ipaddr);
	if ($realipno < 0) {
		$realipno += 4294967296;
	}
	$handle = $ip->filehandle;
	$baseaddr = $ip->databaseaddr;
	$dbcount = $ip->databasecount;
	$dbcolumn = $ip->databasecolumn;

	$low = 0;
	$high = $dbcount;
	$mid = 0;
	$ipfrom = 0;
	$ipto = 0;
	$ipno = 0;

	if ($realipno == MAX_IPV4_RANGE) {
		$ipno = $realipno - 1;
	} else {
		$ipno = $realipno;
	}

	$record = new IP2LocationRecord;
	$record->country_short = NOT_SUPPORTED;
	$record->country_long = NOT_SUPPORTED;
	$record->region = NOT_SUPPORTED;
	$record->city = NOT_SUPPORTED;
	$record->isp = NOT_SUPPORTED;
	$record->latitude = NOT_SUPPORTED;
	$record->longitude = NOT_SUPPORTED;
	$record->domain = NOT_SUPPORTED;
	$record->zipcode = NOT_SUPPORTED;
	$record->timezone = NOT_SUPPORTED;
	$record->netspeed = NOT_SUPPORTED;
	$record->idd_code = NOT_SUPPORTED;
	$record->area_code = NOT_SUPPORTED;
	$record->weather_code = NOT_SUPPORTED;
	$record->weather_name = NOT_SUPPORTED;
	$record->ipaddr = $ipaddr;
	$record->ipno = $realipno;

	while ($low <= $high) {
		$mid = (int)(($low + $high)/2);
		$ipfrom = IP2Location_read32($ip, $baseaddr + $mid * $dbcolumn * 4);
		$ipto = IP2Location_read32($ip, $baseaddr + ($mid + 1) * $dbcolumn * 4);
		if($ipfrom < 0) $ipfrom += pow(2,32);
		if($ipto < 0) $ipto += pow(2,32);

		if (($ipno >= $ipfrom) and ($ipno < $ipto)) {
			if ($mode == COUNTRYSHORT) {
				$record->country_short = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($COUNTRY_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == COUNTRYLONG) {
				$record->country_long = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($COUNTRY_POSITION[$dbtype]-1))+3);
				return $record;
			}
			if ($mode == REGION) {
				$record->region = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($REGION_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == CITY) {
				$record->city = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($CITY_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == ISP) {
				$record->isp = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($ISP_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == LATITUDE) {
				$record->latitude = IP2Location_readFloat($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($LATITUDE_POSITION[$dbtype]-1));
				return $record;
			}
			if ($mode == LONGITUDE) {
				$record->longitude = IP2Location_readFloat($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($LONGITUDE_POSITION[$dbtype]-1));
				return $record;
			}
			if ($mode == DOMAIN) {
				$record->domain = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($DOMAIN_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == ZIPCODE) {
				$record->zipcode = IP2Location_readStr($handle, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($ZIPCODE_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == TIMEZONE) {
				$record->timezone = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4) + 4 * ($TIMEZONE_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == NETSPEED) {
				$record->netspeed = IP2Location_readStr($handle, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4) + 4 * ($NETSPEED_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == IDDCODE) {
				$record->idd_code = IP2Location_readStr($handle, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4) + 4 * ($IDDCODE_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == AREACODE) {
				$record->area_code = IP2Location_readStr($handle, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4) + 4 * ($AREACODE_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == WEATHERCODE) {
				$record->weather_code = IP2Location_readStr($handle, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4) + 4 * ($WEATHERCODE_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == WEATHERNAME) {
				$record->weather_name = IP2Location_readStr($handle, IP2Location_read32($ip, $baseaddr + $mid *($dbcolumn * 4) + 4 * ($WEATHERNAME_POSITION[$dbtype]-1)));
				return $record;
			}
			if ($mode == ALL) {

				if ($COUNTRY_POSITION[$dbtype] != 0) {
					$record->country_short = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($COUNTRY_POSITION[$dbtype]-1)));
					$record->country_long = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($COUNTRY_POSITION[$dbtype]-1))+3);
				}
				if ($REGION_POSITION[$dbtype] != 0) {
					$record->region = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($REGION_POSITION[$dbtype]-1)));
				}
				if ($CITY_POSITION[$dbtype] != 0) {
					$record->city = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($CITY_POSITION[$dbtype]-1)));
				}
				if ($ISP_POSITION[$dbtype] != 0) {
					$record->isp = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($ISP_POSITION[$dbtype]-1)));
				}
				if ($LATITUDE_POSITION[$dbtype] != 0) {
					$record->latitude = IP2Location_readFloat($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($LATITUDE_POSITION[$dbtype]-1));
				}
				if ($LONGITUDE_POSITION[$dbtype] != 0) {
					$record->longitude = IP2Location_readFloat($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($LONGITUDE_POSITION[$dbtype]-1));
				}
				if ($DOMAIN_POSITION[$dbtype] != 0) {
					$record->domain = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($DOMAIN_POSITION[$dbtype]-1)));
				}
				if ($ZIPCODE_POSITION[$dbtype] != 0) {
					$record->zipcode = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($ZIPCODE_POSITION[$dbtype]-1)));
				}
				if ($TIMEZONE_POSITION[$dbtype] != 0) {
					$record->timezone = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($TIMEZONE_POSITION[$dbtype]-1)));
				}
				if ($NETSPEED_POSITION[$dbtype] != 0) {
					$record->netspeed = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($NETSPEED_POSITION[$dbtype]-1)));
				}
				if ($IDDCODE_POSITION[$dbtype] != 0) {
					$record->idd_code = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($IDDCODE_POSITION[$dbtype]-1)));
				}
				if ($AREACODE_POSITION[$dbtype] != 0) {
					$record->area_code = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($AREACODE_POSITION[$dbtype]-1)));
				}
				if ($WEATHERCODE_POSITION[$dbtype] != 0) {
					$record->weather_code = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($WEATHERCODE_POSITION[$dbtype]-1)));
				}
				if ($WEATHERNAME_POSITION[$dbtype] != 0) {
					$record->weather_name = IP2Location_readStr($ip, IP2Location_read32($ip, $baseaddr + ($mid * $dbcolumn * 4) + 4 * ($WEATHERNAME_POSITION[$dbtype]-1)));
				}
				return $record;
			}
		} else {
			if ($ipno < $ipfrom) {
				$high = $mid - 1;
			} else {
				$low = $mid + 1;
			}
		}
	}
	return $record;
}

function IP2Location_get_all ($ip, $ipaddr) {
	if ($ip->{"ipversion"} == IPV6) {
		return IP2Location_get_ipv6_record($ip, $ipaddr, ALL);
	} else {
		return IP2Location_get_record($ip, $ipaddr, ALL);
	}

}

function IP2Location_ip_is_ipv4 ($ipaddr) {

	if( !preg_match('/^[\d\.]+$/', $ipaddr) ) {
		return 0;
	}

	if(preg_match('/^\.|\.\.|\.$/', $ipaddr)) {
		return 0;
	}

	$iparray = preg_split('/\./', $ipaddr);
	if(count($iparray) < 1 or count($iparray) > 4) {
		return 0;
	}

	foreach ($iparray as $ipsub) {
		if($ipsub < 0 or $ipsub > 255) {
			return 0;
		}
	}

	return 1;
}

function IP2Location_ip_is_ipv6 ($ipaddr) {

	$n = substr_count($ipaddr, ":");
	if ($n < 1 or $n > 7) {
		return (0);
	}

	$k = 0;

	foreach (preg_split('/:/',$ipaddr) as $ipsub)
	{
		$k++;
		if ($ipsub == '') {
			continue;
		}
		if (preg_match('/^[a-f\d]{1,4}$/i', $ipsub)) {
			continue;
		}

		if ($k == $n+1) {
			if (IP2Location_ip_is_ipv4($ipsub)) {
				// here we know it is embeded ipv4, should retrieve data from ipv4 db, pending...
				// the result of this will not be valid, since all characters are treated and calculated
				// in hex based.
				// In addition, embeded ipv4 requires 96 '0' bits. We need to check this too.
				continue;
			}
		}
		return 0;
	}

	$m = preg_match_all('/:(?=:)/', $ipaddr, $dummy);
	if ($m > 1 and $n < 7) {
		return 0;
	};

	return 1;
}

function IP2Location_ipv6_to_no ($ipaddr) {

	$n = substr_count($ipaddr, ":");
	if($n < 7) {
		$expanded = "::";
		while($n < 7) {
			$expanded .= ":";
			$n++;
		}

		$ipaddr = preg_replace("/::/", $expanded, $ipaddr);
	}

	$subLoc = 8;
	$ipv6no = "0";
	foreach(preg_split('/:/', $ipaddr) as $ipsub) {
		$subLoc--;
		if($ipsub == '') {
			continue;
		}

		$ipv6no = bcadd( $ipv6no, bcmul(hexdec($ipsub), bcpow(hexdec('0x10000'), $subLoc)));
	}
	return $ipv6no;
}

?>