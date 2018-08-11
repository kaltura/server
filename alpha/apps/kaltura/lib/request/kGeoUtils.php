<?php
/** 
 * @package server-infra
 * @subpackage request
 */
class kGeoUtils 
{
	private static $regions = array(
			"NorthAmerica" => "AG,AI,AW,BB,BL,BM,BQ,BS,BZ,CA,CR,CU,CW,DM,DO,GD,GL,GP,GT,HN,HT,JM,KN,KY,LC,MF,MQ,MS,MX,NI,PA,PM,PR,SV,SX,TC,TT,US,VC,VG,VI",
			"Europe" => "AD,AL,AT,AX,BA,BE,BG,BY,CH,CZ,DE,DK,EE,ES,FI,FO,FR,GB,GG,GI,GR,HR,HU,IE,IM,IS,IT,JE,LI,LT,LU,LV,MC,MD,ME,MK,MT,NL,NO,PL,PT,RO,RS,RU,SE,SI,SK,SM,UA,VA",
			"SouthAmerica" => "BR,CO,AR,PE,VE,CL,EC,BO,PY,UY,GY,SR,GF,FK",
			"Asia" => "AE,AF,AM,AZ,BD,BH,BN,BT,CN,CY,GE,HK,ID,IL,IN,IQ,IR,JO,JP,KG,KH,KP,KR,KW,KZ,LA,LB,LK,MM,MN,MO,MV,MY,NP,OM,PH,PK,PS,QA,SA,SG,SY,TH,TJ,TL,TM,TR,UZ,VN,YE",
			"Oceania" => "AU,CK,FM,GU,KI,MH,NF,NR,NU,NZ,PN,PW,TK,TO,TV,WF,WS",
			"Africa" => "AO,BF,BI,BJ,BW,CD,CF,CG,CI,CM,CV,DJ,DZ,EG,ER,ET,GA,GH,GM,GN,GQ,GW,KE,KM,LR,LS,LY,MA,MG,ML,MR,MU,MW,MZ,NA,NE,NG,RE,RW,SC,SD,SH,SL,SN,SO,SS,ST,SZ,TD,TG,TN,TZ,UG,YT,ZA,ZM,ZW",
			);
		

	public static function isInGeoDistance($coordinates, $range)
	{
		list($latitude1, $longitude1) = $coordinates;
		list($latitude2, $longitude2, $radius) = explode(",", $range);
		$latitudate2 = (double)$latitude2;
		$longitude2 = (double)$longitude2;
		$radius = (double)$radius;
		
		$theta = $longitude1 - $longitude2;
		$distance = sin(deg2rad($latitude1)) * sin(deg2rad($latitude2)) +  cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta));
		$distance = acos($distance);
		$distance = rad2deg($distance);
		$km = $distance * 60 * 1.1515 * 1.609344;

		if (class_exists('KalturaLog'))
			KalturaLog::info("distance ($latitude1,$longitude1) to ($latitude2,$longitude2) is $km , should be less than $radius");

		return $km <= $radius;
	}
	
	public static function countryToRegion($country)
	{
		foreach(self::$regions as $name => $countries)
		{
			if (strpos($countries, $country) !== false)
				return $name;
		}
		
		return "unknown";
	}
}