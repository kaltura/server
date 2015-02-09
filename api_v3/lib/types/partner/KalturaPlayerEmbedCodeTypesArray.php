<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPlayerEmbedCodeTypesArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaPlayerEmbedCodeType");
	}
	
	public static function fromDbArray(array $arr, IResponseProfile $responseProfile = null)
	{
		$ret = new KalturaPlayerEmbedCodeTypesArray();
		foreach($arr as $id => $item)
		{
			$obj = new KalturaPlayerEmbedCodeType();
			$obj->id = $id;
			$obj->fromArray($item);
			$ret[] = $obj;
		}
		return $ret;
	}
}