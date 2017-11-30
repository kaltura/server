<?php
/**
 * @package plugins.elasticSearch
 * @subpackage api.objects
 */
class KalturaEsearchHighlightArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaEsearchHighlight");
	}

	public static function fromDbArray($arr, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$newArr = new KalturaEsearchHighlightArray();
		foreach ( $arr as $obj )
		{
			$nObj = new KalturaEsearchHighlight();
			$nObj->fromObject($obj, $responseProfile);
			$newArr[] = $nObj;
		}

		return $newArr;
	}
}
