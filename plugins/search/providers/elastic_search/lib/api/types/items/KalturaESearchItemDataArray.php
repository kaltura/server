<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchItemDataArray extends KalturaTypedArray
{

    public function __construct()
    {
        return parent::__construct("KalturaESearchItemData");
    }

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaESearchItemDataArray();
		foreach ( $arr as $obj )
		{
			$nObj = KalturaPluginManager::loadObject('KalturaESearchItemData', $obj->getType());
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}

}
