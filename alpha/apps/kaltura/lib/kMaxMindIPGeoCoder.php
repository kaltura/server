<?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'kGeoCoder.php');

$baseDir = KALTURA_ROOT_PATH . DIRECTORY_SEPARATOR . 'vendor/MaxMind';

require("$baseDir/MaxMind/Db/Reader.php");
require("$baseDir/MaxMind/Db/Reader/Decoder.php");
require("$baseDir/MaxMind/Db/Reader/Util.php");
require("$baseDir/MaxMind/Db/Reader/Metadata.php");

require("$baseDir/GeoIP2/Exception/GeoIp2Exception.php");
require("$baseDir/GeoIP2/Exception/AddressNotFoundException.php");
require("$baseDir/GeoIP2/ProviderInterface.php");
require("$baseDir/GeoIP2/Database/Reader.php");
require("$baseDir/GeoIP2/Compat/JsonSerializable.php");
require("$baseDir/GeoIP2/Model/AbstractModel.php");
require("$baseDir/GeoIP2/Model/AnonymousIp.php");
require("$baseDir/GeoIP2/Model/Country.php");
require("$baseDir/GeoIP2/Record/AbstractRecord.php");
require("$baseDir/GeoIP2/Record/AbstractPlaceRecord.php");
require("$baseDir/GeoIP2/Record/Traits.php");
require("$baseDir/GeoIP2/Record/Country.php");
require("$baseDir/GeoIP2/Record/RepresentedCountry.php");
require("$baseDir/GeoIP2/Record/MaxMind.php");
require("$baseDir/GeoIP2/Record/Continent.php");

use GeoIp2\Database\Reader;

class kMaxMindIPGeocoder extends kGeoCoder
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
		return false;
	}

	public function getAnonymousInfo($ip)
	{
		static $reader = null;
		$attr = array();
		
		try {
			if (!$reader)
			{
				$dbFilePath = __DIR__ . '/../../../../../../data'."/MaxMind/Anonymous/GeoIP2-Anonymous-IP.mmdb";
				$reader = new Reader('$dbFilePath');
			}

			$record = $reader->anonymousIp($ip);

			if (!$record->isAnonymous) return "";
			if ($record->isAnonymousVpn) return "anonymousVpn";
			if ($record->isHostingProvider) return "hostingProvider";
			if ($record->isPublicProxy) return "publicProxy";
			if ($record->isTorExitNode) return "torExitNode";
			return "anonymous";
		}
		catch(Exception $e)
		{
		}
		
		return "undefined";
	}

	function iptocountry($ip) 
	{   
		static $reader = null;

		try {
			if (!$reader)
			{
				$dbFilePath = __DIR__ . '/../../../../../../data'."/MaxMind/Country/GeoIP2-Country.mmdb";
				$reader = new Reader('$dbFilePath');
			}

			$country = $reader->country($ip);
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
