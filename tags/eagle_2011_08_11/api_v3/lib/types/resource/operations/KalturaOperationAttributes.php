<?php
/**
 * Base class to all operation attributes types
 * 
 * @package api
 * @subpackage objects
 * @abstract
 */
class KalturaOperationAttributes extends KalturaObject 
{
	public function toAttributesArray()
	{
		return array();
	}
}