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
	
	public function fromDbArray($arr)
	{
		foreach($arr as $id => $item)
		{
			$obj = new KalturaPlayerEmbedCodeType();
			$obj->id = $id;
			$obj->fromArray($item);
			$this[] = $obj;
		}
	}
}