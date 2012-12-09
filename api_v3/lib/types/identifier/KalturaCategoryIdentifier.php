<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaCategoryIdentifier extends KalturaObjectIdentifier
{
	/**
	 * Identifier of the object
	 * @var KalturaCategoryIdentifierField
	 */
	public $identifier;
	
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