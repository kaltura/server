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
		list($latitude2, $longitude2, $distnace) = explode(":", $range);
		
		$theta = $longitude1 - $longitude2;
		$dist = sin(deg2rad($latitude1)) * sin(deg2rad($latitude2)) +  cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$km = $dist * 60 * 1.1515 * 1.609344;

		return $km <= $distance;
	}
}