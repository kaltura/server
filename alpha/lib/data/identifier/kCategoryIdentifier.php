<?php
/**
 * @package Core
 * @subpackage model
 */
class kCategoryIdentifier extends kObjectIdentifier
{
	/* (non-PHPdoc)
	 * @see KObjectIdentifier::retrieveByIdentifier()
	 */
	public function retrieveByIdentifier ($value)
	{
		switch ($this->identifier)
		{
			case CategoryIdentifierField::FULL_NAME:
				return categoryPeer::getByFullNameExactMatch($value);
			case CategoryIdentifierField::ID:
				return categoryPeer::retrieveByPK($value);
			case CategoryIdentifierField::REFERENCE_ID:
				$objects = categoryPeer::getByReferenceId($value);
				return $objects[0];
		}	
	}
}