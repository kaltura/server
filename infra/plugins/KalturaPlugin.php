<?php
abstract class KalturaPlugin implements IKalturaPlugin
{
	public function getInstance($intrface)
	{
		if($this instanceof $intrface)
			return $this;
			
		return null;
	}
	
	public static function isApiV3()
	{
		if (defined("KALTURA_API_V3")) {
			return true;
		}
		
		return false;
	}
}