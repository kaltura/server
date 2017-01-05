<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUrlTokenizerBitGravity extends KalturaHashPatternUrlTokenizer {

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kBitGravityUrlTokenizer();
			
		parent::toObject($dbObject, $skip);
	
		return $dbObject;
	}
}
