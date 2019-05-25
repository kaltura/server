<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */

class KalturaESearchAggregationsArray extends KalturaTypedArray
{

	public function __construct()
	{
		return parent::__construct("KalturaESearchAggregationItem");
	}

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		KalturaLog::debug(print_r($arr, true));
		$newArr = new KalturaESearchAggregationsArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			switch (get_class($obj))
			{
			case 'ESearchCategoryAggregationItem':
				$nObj = new KalturaESearchCategoryAggregationItem();
			break;

			case 'ESearchMetadataAggregationItem':
				$nObj = new KalturaESearchMetadataAggregationItem();
			break;

			case 'ESearchEntryAggregationItem':
				$nObj = new KalturaESearchEntryAggregationItem();
			break;

			case 'ESearchCuepointsAggregationItem':
				$nObj = new KalturaESearchCuepointsAggregationItem ();
			break;

			default:
				$nObj = KalturaPluginManager::loadObject('KalturaESearchAggregationItem', get_class($obj));
			break;
			}

			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

}