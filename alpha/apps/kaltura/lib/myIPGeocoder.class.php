<?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'kGeoCoder.php');

class myIPGeocoder extends kGeoCoder
{
	/* (non-PHPdoc)
	 * @see kGeoCoder::getCountry()
	 */
	public function getCountry($ip)
	{
		return $this->iptocountry($ip);
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
