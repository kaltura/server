<?php

require_once(dirname(__FILE__) . '/kGeoCoder.php');
require_once(dirname(__FILE__) . '/request/kIP2Location.php');

class myIPGeocoder extends kGeoCoder
{
	/* (non-PHPdoc)
	 * @see kGeoCoder::getCountry()
	 */
	public function getCountry($ip)
	{
		return $this->iptocountry($ip);
	}
	
	/* (non-PHPdoc)
	 * @see kGeoCoder::getCoordinates()
	 */
	public function getCoordinates($ip)
	{
		return kIP2Location::ipToCoordinates($ip);
	}

	public function getAnonymousInfo($ip)
	{
		return array("undefined");
	}

	function iptocountry($ip) 
	{   
		return kIP2Location::ipToCountry($ip);
	}
	
	function iptocountryAndCode($ip) 
	{
		return kIP2Location::ipToCountryAndCode($ip);
	}
}
