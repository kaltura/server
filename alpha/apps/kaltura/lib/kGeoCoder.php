<?php
/**
 * @package Core
 * @subpackage AccessControl
 */
abstract class kGeoCoder
{
	/**
	 * @param string $ip
	 * @return string
	 */
	abstract public function getCountry($ip);
	
	/**
	 * @param string $ip
	 * @return array (latitude, longitude)
	 */
	abstract public function getCoordinates($ip);
}