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
	public function getRootObjects(IRelatedObjectPeer $object)
	{
		return array(entryPeer::retrieveByPK($object->getEntryId()));
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::isReferenced()
	 */
	public function isReferenced(IRelatedObjectPeer $object)
	{
		return false;
	}
}
