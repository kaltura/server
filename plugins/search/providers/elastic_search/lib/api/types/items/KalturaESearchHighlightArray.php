<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaESearchHighlightArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaESearchHighlight");
	}

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaESearchHighlightArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaESearchHighlight();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}
}
