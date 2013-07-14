<?php
/**
 * An int representation to return an array of ints
 * 
 * @see KalturaIntegerValueArray
 * @package api
 * @subpackage objects
 */
class KalturaIntegerValue extends KalturaValue
{
	/**
	 * @var int
	 */
    public $value;

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kIntegerValue();
			
		return parent::toObject($dbObject, $skip);
	}
}