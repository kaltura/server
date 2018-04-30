<?php

/**
 * Subclass for representing a row from the 'syndication_feed' table.
 *
 * 
 *
 * @package Core
 * @subpackage model
 */ 
class syndicationFeed extends BasesyndicationFeed implements IBaseObject
{
	const CUSTOM_DATA_MRSS_PARAMETERS = 'mrss_parameters';
	const CUSTOM_DATA_STORAGE_ID = 'storage_id';
	const CUSTOM_DATA_ENTRIES_ORDER_BY = 'entries_order_by';
	const CUSTOM_DATA_ENFORCE_ORDER = 'enforce_order';
	const CUSTOM_DATA_SERVE_PLAY_MANIFEST = 'serve_play_manifest';
	const CUSTOM_DATA_USE_CATEGORY_ENTRIES = 'use_category_entries';
	const CUSTOM_DATA_PLAYER_TYPE = 'player_type';
	const CUSTOM_DATA_FEED_CONTENT_TYPE_HEADER = 'feed_content_type_header';
	const CUSTOM_DATA_ENFORCE_AUTHOR = 'enforce_feed_author';

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
			$existing_object = syndicationFeedPeer::retrieveByPKNoFilter( $id );
			
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
	 * @see BasesyndicationFeed::preSave()
	 */
	public function preSave(PropelPDO $con = null)
	{
		if($this->isNew())
		{
			$this->setServePlayManifest(true);
			$this->setPlayerType(PlayerType::HTML5Player);
		}
		
		return parent::preSave($con);
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/om/BasesyndicationFeed#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);
		
		$objectDeleted = false;
		if($this->isColumnModified(BasesyndicationFeedPeer::STATUS) && $this->getStatus() == self::SYNDICATION_DELETED)
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		return $ret;
	}
	
	/*
	 * @return kMrssParameters
	 */
	public function getMrssParameters()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_MRSS_PARAMETERS);
	}
	
	/**
	 * @param kMrssParameters $mrssParams
	 */
	public function setMrssParameters(kMrssParameters $mrssParams)
	{	
		if (is_array($mrssParams->getItemXpathsToExtend())) {			
			$this->putInCustomData(self::CUSTOM_DATA_MRSS_PARAMETERS, $mrssParams);
		}
	}
	
	/**
	 * @return int
	 */
	public function getStorageId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_STORAGE_ID);
	}
	
	/**
	 * @param int $mrssParams
	 */
	public function setStorageId($storageId)
	{	
		$this->putInCustomData(self::CUSTOM_DATA_STORAGE_ID, $storageId);
	}

	/**
	 * @return string
	 */
	public function getEntriesOrderBy()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ENTRIES_ORDER_BY);
	}

	/**
	 * @param string $entriesOrderBy
	 */
	public function setEntriesOrderBy($entriesOrderBy)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ENTRIES_ORDER_BY, $entriesOrderBy);
	}
	
	/**
	 * @param boolean $enforceOrder
	 */
	public function setEnforceOrder($enforceOrder)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ENFORCE_ORDER, $enforceOrder);
	}

	/**
	 * @param boolean $enforceFeedAuthor
	 */
	public function setEnforceFeedAuthor($enforceFeedAuthor)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ENFORCE_AUTHOR, $enforceFeedAuthor);
	}

	/**
	 * @return boolean
	 */
	public function getEnforceFeedAuthor()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ENFORCE_AUTHOR);
	}


	/**
	 * @return boolean
	 */
	public function getEnforceOrder()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ENFORCE_ORDER);
	}
	
	/**
	 * @param boolean $servePlayManifest
	 */
	public function setServePlayManifest($servePlayManifest)
	{
		$this->putInCustomData(self::CUSTOM_DATA_SERVE_PLAY_MANIFEST, $servePlayManifest);
	}
	
	/**
	 * @return boolean
	 */
	public function getServePlayManifest()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SERVE_PLAY_MANIFEST, null, false);
	}

	/**
	 * @param int $playerType
	 */
	public function setPlayerType($playerType)
	{
		$this->putInCustomData(self::CUSTOM_DATA_PLAYER_TYPE, $playerType);
	}

	/**
	 * @return int
	 */
	public function getPlayerType()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_PLAYER_TYPE, null, PlayerType::KDP);
	}

	/**
	 * @param boolean $v
	 */
	public function setUseCategoryEntries($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_USE_CATEGORY_ENTRIES, $v);
	}
	
	/**
	 * @return boolean
	 */
	public function getUseCategoryEntries()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_USE_CATEGORY_ENTRIES, null, false);
	}
	
	/**
	 * @param boolean $v
	 */
	public function setFeedContentTypeHeader($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FEED_CONTENT_TYPE_HEADER, $v);
	}
	
	/**
	 * @return boolean
	 */
	public function getFeedContentTypeHeader()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_FEED_CONTENT_TYPE_HEADER, null, 'text/xml; charset=utf-8');
	}

	public function init()
	{
	}
	public function getCacheInvalidationKeys()
	{
		return array("syndicationFeed:id=".strtolower($this->getId()));
	}
}
