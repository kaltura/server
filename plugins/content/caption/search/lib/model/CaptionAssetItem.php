<?php


/**
 * Skeleton subclass for representing a row from the 'caption_asset_item' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.captionSearch
 * @subpackage model
 */
class CaptionAssetItem extends BaseCaptionAssetItem implements IIndexable
{
	/**
	 * @var CaptionAsset
	 */
	protected $aAsset = null;
	
	/**
	 * @var entry
	 */
	protected $aEntry = null;
	
	public function getIndexObjectName() {
		return "CaptionAssetItemIndex";
	}
	
	/**
	 * @return CaptionAsset
	 */
	public function getAsset()
	{
		if(!$this->aAsset && $this->getCaptionAssetId())
			$this->aAsset = assetPeer::retrieveById($this->getCaptionAssetId());
			
		return $this->aAsset;
	}
	
	/**
	 * @return entry
	 */
	public function getEntry()
	{
		if(!$this->aEntry && $this->getEntryId())
			$this->aEntry = entryPeer::retrieveByPK($this->getEntryId());
			
		return $this->aEntry;
	}
	
	/* (non-PHPdoc)
	 * @see IIndexable::getIntId()
	 */
	public function getIntId()
	{
		return $this->getId();
	}
	
	/* (non-PHPdoc)
	 * @see IIndexable::indexToSearchIndex()
	 */
	public function indexToSearchIndex()
	{
		kEventsManager::raiseEventDeferred(new kObjectReadyForIndexEvent($this));
	}

	/**
	 * @return string
	 */
	public function getTags()
	{
		return $this->getAsset()->getTags();
	}
	
	/**
	 * @return string
	 */
	public function getPartnerDescription()
	{
		return $this->getAsset()->getPartnerDescription();
	}
	
	/**
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->getAsset()->getLanguage();
	}
	
	/**
	 * @return string
	 */
	public function getLabel()
	{
		return $this->getAsset()->getLabel();
	}
	
	/**
	 * @return string
	 */
	public function getFormat()
	{
		return $this->getAsset()->getContainerFormat();
	}
	
	/**
	 * @return int
	 */
	public function getCaptionParamsId()
	{
		return $this->getAsset()->getFlavorParamsId();
	}
	
	/**
	 * @return int
	 */
	public function getVersion()
	{
		return $this->getAsset()->getVersion();
	}
	
	/**
	 * @return int
	 */
	public function getStatus()
	{
		return $this->getAsset()->getStatus();
	}
	
	/**
	 * @return int
	 */
	public function getSize()
	{
		return $this->getAsset()->getSize();
	}
	
	/**
	 * @return int
	 */
	public function getDefault()
	{
		return $this->getAsset()->getDefault();
	}
	
	/**
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getUpdatedAt($format = 'Y-m-d H:i:s')
	{
		return $this->getAsset()->getUpdatedAt($format);
	}
	
	/**
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDeletedAt($format = 'Y-m-d H:i:s')
	{
		return $this->getAsset()->getDeletedAt($format);
	}

	/* (non-PHPdoc)
	 * @see IIndexable::setUpdatedAt()
	 */
	public function setUpdatedAt($time)
	{
		return $this; // updates nothing
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/Baseentry#postInsert()
	 */
	public function postInsert(PropelPDO $con = null)
	{
		parent::postInsert($con);
	
		if (!$this->alreadyInSave)
			kEventsManager::raiseEvent(new kObjectAddedEvent($this));
	}

	/* (non-PHPdoc)
	 * @see lib/model/om/Baseentry#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);
		
		$objectUpdated = $this->isModified();
			
		$ret = parent::postUpdate($con);
					
		if($objectUpdated)
			kEventsManager::raiseEvent(new kObjectUpdatedEvent($this));
			
		return $ret;
	}
	
	/* (non-PHPdoc)
	 * @see BaseObject::postDelete()
	 */
	public function postDelete(PropelPDO $con = null)
	{
		kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
	}

} // CaptionAssetItem
