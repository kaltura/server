<?php

/**
 * @package api
 * @subpackage objects
 */
class KalturaGenericArray extends KalturaTypedArray
{
	public function __construct()
	{
		parent::__construct('KalturaObject');
	}
}