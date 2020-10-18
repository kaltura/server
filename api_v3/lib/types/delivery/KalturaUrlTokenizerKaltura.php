<?php
/**
 * @package api
 * @subpackage objects
 */

class KalturaUrlTokenizerKaltura  extends KalturaUrlTokenizer
{

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kKalturaUrlTokenizer();

		parent::toObject($dbObject, $skip);

		return $dbObject;
	}

}