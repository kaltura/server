<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUrlTokenizerPlaybackContext extends KalturaUrlTokenizer
{
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kUrlTokenizerPlaybackContext();
		
		parent::toObject($dbObject, $skip);
		
		return $dbObject;
	}
}
