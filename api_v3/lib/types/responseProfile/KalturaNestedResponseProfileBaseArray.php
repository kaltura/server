<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaNestedResponseProfileBaseArray extends KalturaTypedArray
{
	public static function fromDbArray($arr)
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
			$nObj->fromObject($obj);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}

	static function getInstanceByDbObject(IResponseProfileBase $dbObject)
	{
		switch(get_class($dbObject))
		{
			case 'kResponseProfile':
				return new KalturaNestedResponseProfile();
				
			case 'kResponseProfileHolder':
				return new KalturaNestedResponseProfileHolder();
				
			default:
				return KalturaPluginManager::loadObject('KalturaNestedResponseProfileBase', get_class($dbObject));
		}
	}
		
	public function __construct()
	{
		parent::__construct("KalturaNestedResponseProfileBase");	
	}
}