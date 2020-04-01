<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaUrlTokenizerCdnVideo extends KalturaUrlTokenizer
{
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kCdnVideoUrlTokenizer();

		parent::toObject($dbObject, $skip);

		return $dbObject;
	}
}
