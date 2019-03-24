<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchEntryBaseItemArray extends KalturaTypedArray
{
	
	public function __construct()
	{
		return parent::__construct("KalturaESearchEntryBaseItem");
	}
	
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		KalturaLog::debug(print_r($arr, true));
		$newArr = new KalturaESearchEntryBaseItemArray();
		if ($arr == null)
			return $newArr;
		
		foreach ($arr as $obj)
		{
			switch (get_class($obj))
			{
				case 'ESearchEntryItem':
					$nObj = new KalturaESearchEntryItem();
					break;
					
				default:
					$nObj = KalturaPluginManager::loadObject('KalturaESearchEntryBaseItem', get_class($obj));
					break;
			}
			
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
	
}
