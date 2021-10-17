<?php
/**
 * @package api
 * @subpackage objects
 */
 class KalturaActionNameCondition extends KalturaRegexCondition
 {
	 /**
	  * Init object type
	  */
	 public function __construct()
	 {
		 $this->type = KalturaConditionType::VALIDATE_ACTION_NAME;
	 }

	 /* (non-PHPdoc)
	  * @see KalturaObject::toObject()
	  */
	 public function toObject($dbObject = null, $skip = array())
	 {
		 if (!$dbObject)
			 $dbObject = new kActionNameCondition();

		 return parent::toObject($dbObject, $skip);
	 }

 }

