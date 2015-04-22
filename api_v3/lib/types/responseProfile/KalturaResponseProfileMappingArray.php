<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaResponseProfileMappingArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaResponseProfileMappingArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$dbClass = get_class($obj);
			if ($dbClass == 'kResponseProfileMapping')
				$nObj = new KalturaResponseProfileMapping();
			else
				$nObj = KalturaPluginManager::loadObject('KalturaResponseProfileMapping', $dbClass);

			if (is_null($nObj))
				KalturaLog::err('Failed to load api object for '.$dbClass);

			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaResponseProfileMapping");	
	}
}