<?php
/**
 * @package plugins
 * @subpackage api
 */
class KalturaPluginReplacementOptionsArray extends KalturaTypedArray
{
	public function __construct( )
	{
		return parent::__construct ("KalturaPluginReplacementOptionsItem");
	}
}
