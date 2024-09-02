<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaPartnerCatalogItemArray extends KalturaTypedArray
{
	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaPartnerCatalogItemArray();
		if ($arr == null)
			return $newArr;

		foreach ($arr as $obj)
		{
			$newArr[] = KalturaPartnerCatalogItem::getInstance($obj, $responseProfile);
		}
		
		return $newArr;
	}
		
	public function __construct()
	{
		parent::__construct("KalturaPartnerCatalogItem");
	}
}
