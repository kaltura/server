<?php
/**
 * @package plugins.confControl
 * @subpackage api.objects
 */
class KalturaConfigMapArray extends KalturaTypedArray
{
	public function __construct()
	{
		parent::__construct("KalturaConfigMap");
	}
	public function insert(KalturaConfigMap $map)
	{
		$this->array[] = $map;
	}
}