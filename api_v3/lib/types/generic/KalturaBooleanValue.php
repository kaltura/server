<?php
/**
 * A boolean representation to return an array of booleans
 * 
 * @see KalturaBooleanValueArray
 * @package api
 * @subpackage objects
 */
class KalturaBooleanValue extends KalturaValue
{
	/**
	 * @var bool
	 */
    public $value;

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kBooleanValue();
			
		return parent::toObject($dbObject, $skip);
	}
}