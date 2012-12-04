<?php
/**
 * @package api
 * @subpackage objects
 *
 */
class KalturaLiveStreamConfigurationArray extends KalturaTypedArray
{
	public static function fromDbArray(array $pairs = null)
	{
		$array = new KalturaLiveStreamConfigurationArray();
		if($array && is_array($array))
		{
			foreach($array as $object)
			{
				/* @var $object KLiveStreamConfiguration */
				$configObject = new KalturaLiveStreamConfiguration();
				$configObject->protocol = $object->getProtocol();
				$configObject->url = $object->getUrl();
				$array[] = $configObject;
			}
		}
		return $configObject;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaLiveStreamConfiguration");
	}
	
}