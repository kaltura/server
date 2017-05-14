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
	/**
	 * @param string $ip
	 * @return array the anonymous info of the IP such as proxy, hosting provider 
	 */
	abstract public function getAnonymousInfo($ip);
}