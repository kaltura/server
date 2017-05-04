<?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'kGeoCoder.php');

$baseDir = dirname(__FILE__) . '/../../../../vendor/MaxMind';

require_once("$baseDir/MaxMind/Db/Reader.php");
require_once("$baseDir/MaxMind/Db/Reader/Decoder.php");
require_once("$baseDir/MaxMind/Db/Reader/Util.php");
require_once("$baseDir/MaxMind/Db/Reader/Metadata.php");

require_once("$baseDir/GeoIP2/Exception/GeoIp2Exception.php");
require_once("$baseDir/GeoIP2/Exception/AddressNotFoundException.php");
require_once("$baseDir/GeoIP2/ProviderInterface.php");
require_once("$baseDir/GeoIP2/Database/Reader.php");
require_once("$baseDir/GeoIP2/Compat/JsonSerializable.php");
require_once("$baseDir/GeoIP2/Model/AbstractModel.php");
require_once("$baseDir/GeoIP2/Model/AnonymousIp.php");
require_once("$baseDir/GeoIP2/Model/Country.php");
require_once("$baseDir/GeoIP2/Record/AbstractRecord.php");
require_once("$baseDir/GeoIP2/Record/AbstractPlaceRecord.php");
require_once("$baseDir/GeoIP2/Record/Traits.php");
require_once("$baseDir/GeoIP2/Record/Country.php");
require_once("$baseDir/GeoIP2/Record/RepresentedCountry.php");
require_once("$baseDir/GeoIP2/Record/MaxMind.php");
require_once("$baseDir/GeoIP2/Record/Continent.php");

use GeoIp2\Database\Reader;

class kMaxMindIPGeocoder extends kGeoCoder
{
	
	static $readerAnonymous = null;
	static $readerCountry = null;
	
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
		return false;
	}

	public function getAnonymousInfo($ip)
	{
		$attr = array();
		
		try {
			if (!self::$readerAnonymous)
			{
				$dbFilePath = __DIR__ . '/../../../../../data/MaxMind/Anonymous/GeoIP2-Anonymous-IP.mmdb';
				self::$readerAnonymous = new Reader($dbFilePath);
			}

			$record = self::$readerAnonymous->anonymousIp($ip);
			
			if ($record->isAnonymous) $attr[] = "anonymous";
			if ($record->isAnonymousVpn) $attr[] = "anonymousVpn";
			if ($record->isHostingProvider) $attr[] = "hostingProvider";
			if ($record->isPublicProxy) $attr[] = "publicProxy";
			if ($record->isTorExitNode) $attr[] = "torExitNode";
			return $attr;
		}
		catch(Exception $e)
		{
		}
		
		return array("undefined");
	}

	function iptocountry($ip) 
	{   
		try {
			if (!self::$readerCountry)
			{
				$dbFilePath = __DIR__ . '/../../../../../data/MaxMind/Country/GeoIP2-Country.mmdb';
				self::$readerCountry = new Reader($dbFilePath);
			}

			$country = self::$readerCountry->country($ip);
			return $country->country->isoCode;
		}
		catch(Exception $e)
		{
		}
		
		return "";
	}
	
	function iptocountryAndCode($ip) 
	{
		return null;
}
}
