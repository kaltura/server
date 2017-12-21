<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorCatalogItemArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaVendorCatalogItemArray();
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