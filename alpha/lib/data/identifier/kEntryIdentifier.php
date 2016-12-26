<?php
/**
 * @package Core
 * @subpackage model
 */
class kEntryIdentifier extends kObjectIdentifier
{
	/* (non-PHPdoc)
	 * @see KObjectIdentifier::retrieveByIdentifier()
	 */
	public function retrieveByIdentifier($value, $partnerId = null)
	{
		switch ($this->identifier)
		{
			case EntryIdentifierField::ID:
				return entryPeer::retrieveByPK($value);
			case EntryIdentifierField::REFERENCE_ID:
				return entryPeer::retrieveByReferenceId($value);
				
		}
		
	}
}