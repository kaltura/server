<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUrlTokenizerLimeLight extends KalturaUrlTokenizer {

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kLimeLightUrlTokenizer();
			
		parent::toObject($dbObject, $skip);
	
		return $dbObject;
	}
}
