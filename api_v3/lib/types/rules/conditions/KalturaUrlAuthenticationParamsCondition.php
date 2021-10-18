<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUrlAuthenticationParamsCondition extends KalturaCondition
{
	/**
	 * Init object type
	 */
	public function __construct() 
	{
		$this->type = ConditionType::URL_AUTH_PARAMS;
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kUrlAuthenticationParamsCondition();
			
		return parent::toObject($dbObject, $skip);
	}
}
