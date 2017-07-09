<?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'kGeoCoder.php');

class kDigitalElementIPGeocoder extends kGeoCoder
{
	const RECORD_LEN = 10;
	
	static $readerAnonymous = null;
	static $dataOffset;
	static $lastRecord;
	static $typeLookup;
	static $descLookup;
	
	/* (non-PHPdoc)
	 * @see kGeoCoder::getCountry()
	 */
	public function getCountry($ip)
	{
		return false;
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
				$dbFilePath = __DIR__ . '/../../../../../data/DigitalElement/Anonymous/kanonymous.bin';
				self::$readerAnonymous = fopen($dbFilePath, "rb");
				
				// read proxy type and description lookup
				$types = trim(fgets(self::$readerAnonymous));
				self::$typeLookup = explode(",", $types);
				
				$descriptions = trim(fgets(self::$readerAnonymous));
				self::$descLookup = explode(",", $descriptions);
				
				self::$dataOffset = ftell(self::$readerAnonymous);
				self::$lastRecord = (filesize($dbFilePath) - self::$dataOffset) / self::RECORD_LEN - 1;
			}

			$ipAddr = ip2long($ip);
			$low = 0;
			$high = self::$lastRecord;
			while($high >= $low)
			{
				$mid = (int)floor(($high + $low) / 2);
				fseek(self::$readerAnonymous, self::$dataOffset + $mid * self::RECORD_LEN);
				$record = fread(self::$readerAnonymous, self::RECORD_LEN);
				$arr = unpack("LstartIp/LendIp", $record);
				$startIp = $arr["startIp"];
				$endIp = $arr["endIp"];

				if ($ipAddr < $startIp)
				{
						$high = $mid - 1;
				}
				elseif ($ipAddr > $endIp)
				{
						$low = $mid + 1;
				}
				else {
						$arr = unpack("CproxyType/CproxyDescription", substr($record, 8));
						$res = array(self::$typeLookup[$arr["proxyType"]], self::$descLookup[$arr["proxyDescription"]]);
						return $res;
				}
			}
		}
		catch(Exception $e)
		{
		}
		
		return array("undefined");
	}

	function iptocountry($ip) 
	{   
		return "";
	}
	
	function iptocountryAndCode($ip) 
	{
		return null;
	}
}

