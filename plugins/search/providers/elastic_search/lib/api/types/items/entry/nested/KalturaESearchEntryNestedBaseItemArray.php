<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchEntryNestedBaseItemArray extends KalturaTypedArray
{
	
	public function __construct()
	{
		return parent::__construct("KalturaESearchEntryNestedBaseItem");
	}
	
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		KalturaLog::debug(print_r($arr, true));
		$newArr = new KalturaESearchEntryNestedBaseItemArray();
		if ($arr == null)
			return $newArr;
		
		foreach ($arr as $obj)
		{
			switch (get_class($obj))
			{
				case 'ESearchMetadataItem':
					$nObj = new KalturaESearchEntryMetadataItem();
					break;
				
				case 'ESearchCuePointItem':
					$nObj = new KalturaESearchCuePointItem();
					break;
				
				case 'ESearchCaptionItem':
					$nObj = new KalturaESearchCaptionItem();
					break;
				
				case 'ESearchNestedOperator':
					$nObj = new KalturaESearchNestedOperator();
					break;
				
				default:
					$nObj = KalturaPluginManager::loadObject('KalturaESearchEntryNestedBaseItem', get_class($obj));
					break;
			}
			
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}
		
		return $newArr;
	}
}
