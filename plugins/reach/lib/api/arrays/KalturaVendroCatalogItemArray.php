<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 */
class KalturaVendroCatalogItemArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaVendroCatalogItemArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$newArr[] = KalturaVendorCatalogItem::getInstance($obj, $responseProfile);
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaVendorCatalogItem");	
	}
}