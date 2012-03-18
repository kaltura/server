<?php
/**
 * Evaluates PHP statement, depends on the execution context
 * 
 * @package api
 * @subpackage objects
 */
class KalturaEvalStringField extends KalturaStringField
{
	/**
	 * PHP code
	 * @var string
	 * @requiresPermission all
	 */
	public $code;
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kEvalStringField();
			
		return parent::toObject($dbObject, $skip);
	}
}