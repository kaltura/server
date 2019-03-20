<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchUserBaseItemArray extends KalturaTypedArray
{
	
	public function __construct()
	{
		return parent::__construct("KalturaESearchUserBaseItem");
	}
	
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		KalturaLog::debug(print_r($arr, true));
		$newArr = new KalturaESearchUserBaseItemArray();
		if ($arr == null)
			return $newArr;
		
		foreach ($arr as $obj)
		{
			switch (get_class($obj))
			{
				case 'ESearchUserItem':
					$nObj = new KalturaESearchUserItem();
					break;
				
				default:
					$nObj = KalturaPluginManager::loadObject('KalturaESearchUserBaseItem', get_class($obj));
					break;
			}
			
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
}
