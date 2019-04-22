<?php

class kSearchUtils
{
	public static function getSkipRepetitiveUpdatesValue($repetitiveUpdatesConfigKey, $className)
	{
		$skipRepetitiveUpdatesConfig = kConf::getMap($repetitiveUpdatesConfigKey);
		
		$updatesKey = strtolower(kCurrentContext::getCurrentPartnerId()."_".$className."_".kCurrentContext::$service."_".kCurrentContext::$action);
		if(isset($skipRepetitiveUpdatesConfig[$updatesKey]))
		{
			return array($skipRepetitiveUpdatesConfig[$updatesKey], $updatesKey);
		}
		
		$updatesKey = strtolower($className."_".kCurrentContext::$service."_".kCurrentContext::$action);
		if(isset($skipRepetitiveUpdatesConfig[$updatesKey]))
		{
			return array($skipRepetitiveUpdatesConfig[$updatesKey], $updatesKey);
		}
		
		$ksPuserId = kCurrentContext::$ks_uid;
		if(isset($ksPuserId))
		{
			//Replace dots with underscore since ini file do not support dont in the key name
			$ksPuserId = str_replace(".", "_", $ksPuserId);
			$updatesKey = strtolower($className."_".kCurrentContext::$service."_".kCurrentContext::$action."_".$ksPuserId);
			if(isset($skipRepetitiveUpdatesConfig[$updatesKey]))
			{
				return array($skipRepetitiveUpdatesConfig[$updatesKey], $updatesKey);
			}
		}
		
		$updatesKey = strtolower(kCurrentContext::getCurrentPartnerId()."_".$className);
		if(isset($skipRepetitiveUpdatesConfig[$updatesKey]))
		{
			return array($skipRepetitiveUpdatesConfig[$updatesKey], $updatesKey);
		}
		
		$updatesKey = strtolower($className);
		if(isset($skipRepetitiveUpdatesConfig[$updatesKey]))
		{
			return array($skipRepetitiveUpdatesConfig[$updatesKey], $updatesKey);
		}
		
		return array(null, null);
	}
}