<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaObjectArray extends KalturaTypedArray
{
	public function __construct()
	{
		parent::__construct('KalturaObject');
	}
}