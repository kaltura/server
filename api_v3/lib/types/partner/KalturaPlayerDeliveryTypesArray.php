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
		$ret = new KalturaPlayerDeliveryTypesArray();
		foreach($arr as $id => $item)
		{
			$obj = new KalturaPlayerDeliveryType();
			$obj->id = $id;
			$obj->fromArray($item);
			$obj->enabledByDefault = (bool)$obj->enabledByDefault;
			$ret[] = $obj;
		}
		return $ret;
	}
}