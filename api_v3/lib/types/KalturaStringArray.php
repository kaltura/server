<?php
/**
 * An array of KalturaString
 * 
 * @package api
 * @subpackage objects
 */
class KalturaStringArray extends KalturaTypedArray
{
	public function __construct()
	{
		return parent::__construct("KalturaString");
	}
}
?>