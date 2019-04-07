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
				
				case 'ESearchOperator':
					$nObj = new KalturaESearchEntryOperator();
					break;
				
				case 'ESearchMetadataItem':
					$nObj = new KalturaESearchEntryMetadataItem();
					break;
				
				case 'ESearchCuePointItem':
					$nObj = new KalturaESearchCuePointItem();
					break;
				
				case 'ESearchCaptionItem':
					$nObj = new KalturaESearchCaptionItem();
					break;
				
				case 'ESearchCategoryEntryNameItem':
					$nObj = new KalturaESearchCategoryEntryItem();
					break;
				
				case 'ESearchUnifiedItem':
					$nObj = new KalturaESearchUnifiedItem();
					break;
				
				case 'ESearchNestedOperator':
					$nObj = new KalturaESearchNestedOperator();
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
