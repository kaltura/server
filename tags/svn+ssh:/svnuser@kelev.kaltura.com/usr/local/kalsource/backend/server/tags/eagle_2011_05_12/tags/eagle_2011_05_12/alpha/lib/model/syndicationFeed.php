<?php

/**
 * Subclass for representing a row from the 'syndication_feed' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class syndicationFeed extends BasesyndicationFeed
{
	// copied from KalturaSyndicationFeedStatus
	const SYNDICATION_DELETED = -1;
	const SYNDICATION_ACTIVE = 1;
	
	// don't stop until a unique hash is created for this object
	private static function calculateId ( )
	{
		$dc = kDataCenterMgr::getCurrentDc();
		for ( $i = 0 ; $i < 10 ; ++$i)
		{
			$id = $dc["id"].'_'.kString::generateStringId();
			$existing_object = entryPeer::retrieveByPk( $id );
			
			if ( ! $existing_object ) return $id;
		}
		
		die();
	}
        
	public function save(PropelPDO $con = null)
	{
		$is_new = false;
		if ( $this->isNew() )
		{
			$this->setId(self::calculateId());
		}
		
		$is_new = true;
		$res = parent::save( $con );
		if ($is_new)
		{
			// when retrieving the entry - ignore thr filter - when in partner has moderate_content =1 - the entry will have status=3 and will fail the retrieveByPk 
			syndicationFeedPeer::setUseCriteriaFilter(false);
			$obj = syndicationFeedPeer::retrieveByPk($this->getId());
			$this->setIntId($obj->getIntId());
			syndicationFeedPeer::setUseCriteriaFilter(true);
		}                
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/BasesyndicationFeed#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		$objectDeleted = false;
		if($this->isColumnModified(BasesyndicationFeedPeer::STATUS) && $this->getStatus() == self::SYNDICATION_DELETED)
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return $ret;
	}
}
