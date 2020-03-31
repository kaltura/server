<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCdnVideoTokenizer extends KalturaUrlTokenizer
{
	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new kCdnVideoTokenizer();

		parent::toObject($dbObject, $skip);

		return $dbObject;
	}
}
