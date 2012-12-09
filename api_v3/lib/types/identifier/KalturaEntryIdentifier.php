<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEntryIdentifier extends KalturaObjectIdentifier
{
	/**
	 * Identifier of the object
	 * @var KalturaEntryIdentifierField
	 */
	public $identifier;
	
	/* (non-PHPdoc)
	 * @see KalturaObjectIdentifier::toObject()
	 */
	public function toObject ($dbObject = null, $propsToSkip = null)
	{
		if (!$dbObject)
			$dbObject = new kEntryIdentifier();

		return parent::toObject($dbObject, $propsToSkip);
	}
	
}