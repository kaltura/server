<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPlayerDeliveryTypesArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaPlayerDeliveryType");
	}

	public static function fromDbArray(array $arr)
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