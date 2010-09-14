<?php
class KalturaTestsHelpers
{
	static $_defaultUserId = "UnitTestUser";
	
	static function getPartner($partnerId = null)
	{
		if(!is_null($partnerId))
		{
			return PartnerPeer::retrieveByPK($partnerId);
		}
		
		$partnerName = "Unit Test Partner";
		
		$c = new Criteria();
		$c->add(PartnerPeer::PARTNER_NAME, $partnerName);
		
		$partner = PartnerPeer::doSelectOne($c);
		
		if (!$partner)
		{
			$partnerRegistration = new myPartnerRegistration();
			$partner = $partnerRegistration->initNewPartner($partnerName, $partnerName, "unittest@kaltura.com", "commercial_use", "yes", $partnerName, $partnerName);
		}
		
		return $partner;
	}
	
	static function getPartnerId()
	{
		return self::getPartner()->getId();
	}
	
	static function createPartner($partnerName, $partnerEmail)
	{
		$partnerRegistration = new myPartnerRegistration();
		$partnerTemp = new Partner();
		$partnerTemp->setType(Partner::PARTNER_TYPE_OTHER);
		$partner = $partnerRegistration->initNewPartner($partnerName, $partnerName, $partnerEmail, "commercial_use", "yes", $partnerName, $partnerName, null, $partnerTemp);
		
		return $partner;
	}
	
	static function getUserId()
	{
		return self::$_defaultUserId;
	}
	
	static function getNormalKs($partnerId = null, $userId = null, $expiry = null, $privileges = null)
	{
	    if ($partnerId === null)
	        $partnerId = self::getPartner()->getId();
	        
		$partner = PartnerPeer::retrieveByPK($partnerId);
		
		if ($userId === null)
		    $userId = self::$_defaultUserId;
		    
	    if ($expiry === null)
	    	$expiry = 86400;
	    	
    	if ($privileges === null)
    		$privileges = "";

	    $ks = "";
		kSessionUtils::startKSession($partnerId, $partner->getSecret(), $userId, $ks, $expiry, 0, "", $privileges);
		return $ks;
	}
	
	static function getAdminKs($partnerId = null, $userId = null)
	{
		if ($partnerId === null)
	        $partnerId = self::getPartner()->getId();

        $partner = PartnerPeer::retrieveByPK($partnerId);
		$privileges = "";
		
		if ($userId === null)
		    $userId = self::$_defaultUserId;
		    
	    $ks = "";
		kSessionUtils::startKSession($partnerId, $partner->getAdminSecret(), $userId, $ks, 86400, 2, "", $privileges);
		return $ks;
	}
	
	static function getServiceInitializedForAction($serviceName, $actionName, $partnerId = null, $userId = null, $ks = null)
	{
	    if ($partnerId === null)
	        $partnerId = self::getPartner()->getId();

        if ($userId === null)
	        $userId = self::$_defaultUserId;
	    
        if ($ks === null)
	        $ks = self::getNormalKs();
	        
	    $services = KalturaServicesMap::getMap();
	    $serviceName = strtolower($serviceName);
		require_once($services[$serviceName]);
		
		$serviceClass = $serviceName."Service";
		$serviceInstance = new $serviceClass();
		
		$serviceInstance->initService($partnerId, $userId, $ks, $serviceName, $actionName);
		
		return $serviceInstance;
	}
	
	static function getRandomString($length = null)
	{
		if ($length === null)
			$length = mt_rand(2,6);
			
	    $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
	    $string = "";
	    for ($p = 0; $p < $length; $p++)
    	{
			$string .= $characters[mt_rand(0, strlen($characters) - 1)];
	    }
	    return $string;
	}
	
	static function getRandomText($numOfWords, $minLength = 2, $maxLength = 6)
	{
		$string = "";
		if ($numOfWords <= 0)
			$numOfWords = 1;
		for($i = 0; $i < $numOfWords; $i++)
		{
			$string .= self::getRandomString(mt_rand($minLength, $maxLength));
			$string .= " ";
		}
		$string = substr($string, 0, strlen($string) - 1);
	    return $string;
	}
	
	static function getRandomTags($numOfTags)
	{
		$text = self::getRandomText($numOfTags);
		return str_replace(" ", ", ", $text);
	}
	
	static function getRandomEmail()
	{
		return self::getRandomString() . "@"  . self::getRandomString() . ".com";
	}
	
	static function getRandomTimeStamp($negative = false)
	{
		if ($negative)
			return mt_rand(0 - PHP_INT_MAX, PHP_INT_MAX);
		else
			return mt_rand(0, PHP_INT_MAX);
	}
	
	static function getRandomDateAsTimeStamp($negative = false)
	{
		//$timestamp = self::getRandomTimeStamp($negative); // remarked by Tan-Tan
		$timestamp = self::getRandomNumber(mktime(0, 0, 0, 0, 0, 0), time());
		
		$year = (int)date("Y", $timestamp);
		$day = (int)date("J", $timestamp);
		$month = (int)date("n", $timestamp);
		$dateOnly = mktime(0, 0, 0, $month, $day, $year);
		
		return $dateOnly;
	}
	
	static function getRandomNumber($min, $max)
	{
		return mt_rand($min, $max);
	}
	
	static function getDummyFlvFilePath()
	{
		$path = pathinfo(__FILE__, PATHINFO_DIRNAME) . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "files" . DIRECTORY_SEPARATOR . "kaltura_logo_animated_black.flv";
		if (!file_exists($path))
			throw new Exception("Dummy FLV file was not found");
			
		return $path; 
	}
}