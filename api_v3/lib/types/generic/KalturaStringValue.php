<?php
/**
 * A string representation to return an array of strings
 * 
 * @see KalturaStringValueArray
 * @package api
 * @subpackage objects
 */
class KalturaStringValue extends KalturaValue
{
	/**
	 * @var string
	 */
    public $value;

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kStringValue();
			
		return parent::toObject($dbObject, $skip);
	}
}