<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCategoryIdentifier extends KalturaObjectIdentifier
{
	/* (non-PHPdoc)
	 * @see KalturaObjectIdentifier::toObject()
	 */
	public function toObject ($dbObject = null, $propsToSkip = null)
	{
		if (!$dbObject)
			$dbObject = new kCategoryIdentifiers();

		return parent::toObject($dbObject, $propsToSkip);
	}
}