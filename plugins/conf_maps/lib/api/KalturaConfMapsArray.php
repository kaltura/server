<?php
/**
 * @package plugins.confMaps
 * @subpackage api.objects
 */
class KalturaConfMapsArray extends KalturaTypedArray
{
	public function __construct()
	{
		parent::__construct("KalturaConfMaps");
	}
	public function insert(KalturaConfMaps $map)
	{
		$this->array[] = $map;
	}
}