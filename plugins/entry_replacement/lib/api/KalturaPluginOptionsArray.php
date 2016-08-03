<?php
/**
 * @package plugins
 * @subpackage api
 */
class KalturaPluginOptionsArray extends KalturaTypedArray
{
	public function __construct( )
	{
		return parent::__construct ("KalturaPluginReplacementOptionsItem");
	}
}
