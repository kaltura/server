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
	
	public function fromDbArray($arr)
	{
		foreach($arr as $id => $item)
		{
			$obj = new KalturaPlayerDeliveryType();
			$obj->id = $id;
			$obj->fromArray($item);
			$this[] = $obj;
		}
	}
}