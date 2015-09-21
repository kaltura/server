<?php

/**
 * Subclass for performing query and update operations on the 'track_entry' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class TrackEntryPeer extends BaseTrackEntryPeer implements IRelatedObjectPeer
{
	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getRootObjects()
	 */
	public function getRootObjects(IRelatedObject $object)
	{
		return array(entryPeer::retrieveByPK($object->getEntryId()));
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::isReferenced()
	 */
	public function isReferenced(IRelatedObject $object)
	{
		return false;
	}
}
