<?php

class kSearchUtils
{
	public static function getSkipRepetitiveUpdatesValue($repetitiveUpdatesConfigKey, $className)
	{
		$skipRepetitiveUpdatesConfig = kConf::get($repetitiveUpdatesConfigKey, 'local', array());
		
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