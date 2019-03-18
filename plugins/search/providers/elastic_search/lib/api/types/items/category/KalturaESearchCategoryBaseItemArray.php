<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchCategoryBaseItemArray extends KalturaTypedArray
{
	
	public function __construct()
	{
		return parent::__construct("KalturaESearchCategoryBaseItem");
	}
	
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		KalturaLog::debug(print_r($arr, true));
		$newArr = new KalturaESearchCategoryBaseItemArray();
		if ($arr == null)
			return $newArr;
		
		foreach ($arr as $obj)
		{
			switch (get_class($obj))
			{
				case 'ESearchCategoryItem':
					$nObj = new KalturaESearchCategoryItem();
					break;
				
				default:
					$nObj = KalturaPluginManager::loadObject('KalturaESearchCategoryBaseItem', get_class($obj));
					break;
			}
			
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
}
