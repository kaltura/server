<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaNestedResponseProfileBaseArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaResponseProfileBase $responseProfile = null)
	{
		$newArr = new KalturaNestedResponseProfileBaseArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$nObj = self::getInstanceByDbObject($obj);
			if(!$nObj)
			{
				KalturaLog::alert("Object [" . get_class($obj) . "] type [" . $obj->getType() . "] could not be translated to API object");
				continue;
			}
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}

	static function getInstanceByDbObject(IResponseProfileBase $object)
	{
		switch(get_class($object))
		{
			case 'kResponseProfile':
				return new KalturaNestedResponseProfile();
				
			case 'kResponseProfileHolder':
				return new KalturaNestedResponseProfileHolder();
				
			default:
				return KalturaPluginManager::loadObject('KalturaNestedResponseProfileBase', get_class($object));
		}
	}
		
	public function __construct()
	{
		parent::__construct("KalturaNestedResponseProfileBase");	
	}
}