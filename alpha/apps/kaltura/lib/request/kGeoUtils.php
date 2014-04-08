<?php
/** 
 * @package server-infra
 * @subpackage request
 */
class kGeoUtils 
{
	public static function isInGeoDistance($coordinates, $range)
	{
		list($latitude1, $longitude1) = $coordinates;
		list($latitude2, $longitude2, $radius) = explode(":", $range);
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
}