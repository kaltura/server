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
	public function getTrackEntryParentObjects(TrackEntry $object)
	{
		return array(entryPeer::retrieveByPK($object->getEntryId()));
	}
	
	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getParentObjects()
	 */
	public function getParentObjects(IBaseObject $object)
	{
		return $this->getTrackEntryParentObjects($object);
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::getRootObjects()
	 */
	public function getRootObjects(IBaseObject $object)
	{
		return $this->getParentObjects($object);
	}

	/* (non-PHPdoc)
	 * @see IRelatedObjectPeer::isReferenced()
	 */
	public function isReferenced(IBaseObject $object)
	{
		return false;
	}
}
