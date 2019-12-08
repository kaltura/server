<?php
/**
 * @package api
 * @subpackage objects
 */

class KalturaHttpHeaderCondition extends KalturaRegexCondition
{
	/**
	 * Init object type
	 */
	public function __construct()
	{
		$this->type = ConditionType::HTTP_HEADER;
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbObject = null, $skip = array())
	{
		if(!$dbObject)
			$dbObject = new kHttpHeaderCondition();

		return parent::toObject($dbObject, $skip);
	}

}