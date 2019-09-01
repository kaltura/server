<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchOrderByItemArray extends KalturaTypedArray
{

    public function __construct()
    {
        return parent::__construct("KalturaESearchOrderByItem");
    }
	
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		KalturaLog::debug(print_r($arr, true));
		$newArr = new KalturaESearchOrderByItemArray();
		if ($arr == null)
			return $newArr;
		
		foreach ($arr as $obj)
		{
			switch (get_class($obj))
			{
				case 'ESearchEntryOrderByItem':
					$nObj = new KalturaESearchEntryOrderByItem();
					break;
				
				case 'ESearchCategoryOrderByItem':
					$nObj = new KalturaESearchCategoryOrderByItem();
					break;
				
				case 'ESearchUserOrderByItem':
					$nObj = new KalturaESearchUserOrderByItem();
					break;
				
				default:
					$nObj = KalturaPluginManager::loadObject('KalturaESearchOrderByItem', get_class($obj));
					break;
			}
			
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
}
