<?php

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'kGeoCoder.php');

class kDigitalElementIPGeocoder extends kGeoCoder
{
	const RECORD_LEN = 48;
	
	static $readerAnonymous = null;
	static $lastRecord;
	
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
				self::$lastRecord = filesize($dbFilePath) / self::RECORD_LEN - 1;
			}

			$ipAddr = ip2long($ip);
			$low = 0;
			$high = self::$lastRecord;
			while($high >= $low)
			{
				$mid = (int)floor(($high + $low) / 2);
				fseek(self::$readerAnonymous, $mid * self::RECORD_LEN);
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
						$arr = unpack("A20proxyType/A20proxyDescription", substr($record, 8));
						return array($arr["proxyType"], $arr["proxyDescription"]);
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
