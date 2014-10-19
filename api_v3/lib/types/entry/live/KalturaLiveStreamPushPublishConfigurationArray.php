<?php
/**
 * @package api
 * @subpackage objects
 *
 */
class KalturaLiveStreamPushPublishConfigurationArray extends KalturaTypedArray
{
	/**
	 * Returns API array object from regular array of database objects.
	 * @param array $dbArray
	 * @return KalturaLiveStreamPushPublishConfiguration
	 */
	public static function fromDbArray(array $dbArray = null)
	{
		$array = new KalturaLiveStreamConfigurationArray();
		if($dbArray && is_array($dbArray))
		{
			foreach($dbArray as $object)
			{
				/* @var $object kLiveStreamPushPublishConfiguration */
				$configObject = KalturaLiveStreamPushPublishConfiguration::getInstance($object->getProtocol());
				$configObject->fromObject($object);
				$array[] = $configObject;
			}
		}
		return $array;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaLiveStreamPushPublishConfiguration");
	}
	
	/* (non-PHPdoc)
	 * @see KalturaTypedArray::toObjectsArray()
	 */
	public function toObjectsArray()
	{
		$objects = $this->toArray();
		for ($i = 0; $i < count($objects); $i++)
		{
			for ($j = $i+1; $j <count($objects); $j++ )
			{
				if ($objects[$i]->protocol == $objects[$j]->protocol)
				{
					unset($objects[$i]);
				}
			}
		}
		
		$ret = array();
		foreach ($objects as $object)
		{
			$ret[] = $object->toObject();
		}
		
		return $ret;
	}
	
}