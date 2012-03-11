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
}