<?php
/**
 * @package api
 * @subpackage objects
 *
 */
class KalturaLiveStreamConfigurationArray extends KalturaTypedArray
{
	public static function fromDbArray(array $dbArray = null)
	{
		$array = new KalturaLiveStreamConfigurationArray();
		if($dbArray && is_array($dbArray))
		{
			foreach($dbArray as $object)
			{
				/* @var $object KLiveStreamConfiguration */
				$configObject = new KalturaLiveStreamConfiguration();
				$configObject->fromObject($object);
			}
		}
		return $configObject;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaLiveStreamConfiguration");
	}
	
	/**
	 * Function extracts the first item in the array where the property $propertyName has the value $propertyValue
	 * @param string $propertyName
	 * @param string $propertyValue
	 * @return KalturaLiveStreamConfiguration
	 */
	public function returnSingleItemByPropertyValue ($propertyName, $propertyValue)
	{
		foreach ($this->toArray() as $config)
		{
			/* @var $config KalturaLiveStreamConfiguration */
			if (property_exists("KalturaLiveStreamConfiguration", $propertyName))
			{
				if ($config->$propertyName == $propertyValue)
				{
					return $config;
				}
			}
				
		}

		return null;
	}
	
	public function toObjectsArray()
	{
		$objects = $this->toArray();
		for ($i = 0; $i < count($objects); $i++)
		{
			for ($j = $i; $j <count($objects); $j++ )
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