<?php
/**
 * @package api
 * @subpackage objects
 *
 */
class KalturaLiveStreamConfigurationArray extends KalturaTypedArray
{
	/**
	 * Returns API array object from regular array of database objects.
	 * @param array $dbArray
	 * @return KalturaLiveStreamConfiguration
	 */
	public static function fromDbArray(array $dbArray = null)
	{
		$array = new KalturaLiveStreamConfigurationArray();
		if($dbArray && is_array($dbArray))
		{
			foreach($dbArray as $object)
			{
				if ($object instanceof kLiveStreamAkamaiConfiguration)
				{
					/* @var $object kLiveStreamAkamaiConfiguration */
					$configObject = new KalturaLiveStreamAkamaiConfiguration();
					$configObject->fromObject($object);
					$array[] = $configObject;
				}
				else
				{
					/* @var $object kLiveStreamConfiguration */
					$configObject = new KalturaLiveStreamConfiguration();
					$configObject->fromObject($object);
					$array[] = $configObject;
				}
			}
		}
		return $array;
	}
	
	public function __construct()
	{
		return parent::__construct("KalturaLiveStreamConfiguration");
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