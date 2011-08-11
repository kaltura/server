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
	 * @see IIndexable::getObjectIndexName()
	 */
	public function getObjectIndexName()
	{
		return CaptionSearchPlugin::INDEX_NAME;
	}

	/* (non-PHPdoc)
	 * @see IIndexable::getIndexFieldsMap()
	 */
	public function getIndexFieldsMap()
	{
		return array(
			'entry_id' => 'entryId',
			'caption_asset_id' => 'captionAssetId',
			'tags' => 'tags',
			'content' => 'content',
			'partner_description' => 'partnerDescription',
			'language' => 'language',
			'label' => 'label',
			'format' => 'format',
			
			'int_id' => 'intId',
			'caption_params_id' => 'captionParamsId',
			'partner_id' => 'partnerId',
			'version' => 'version',
			'caption_asset_status' => 'status',
			'size' => 'size',
			'is_default' => 'default',
			'start_time' => 'startTime',
			'end_time' => 'endTime',
			
			'created_at' => 'createdAt',
			'updated_at' => 'updatedAt',
			
			'str_entry_id' => 'entryId',
			'str_caption_asset_id' => 'id',
		);
	}

	private static $indexFieldTypes = array(
		'entry_id' => IIndexable::FIELD_TYPE_STRING,
		'caption_asset_id' => IIndexable::FIELD_TYPE_STRING,
		'tags' => IIndexable::FIELD_TYPE_STRING,
		'content' => IIndexable::FIELD_TYPE_STRING,
		'partner_description' => IIndexable::FIELD_TYPE_STRING,
		'language' => IIndexable::FIELD_TYPE_STRING,
		'label' => IIndexable::FIELD_TYPE_STRING,
		'format' => IIndexable::FIELD_TYPE_STRING,
		
		'int_id' => IIndexable::FIELD_TYPE_INTEGER,
		'caption_params_id' => IIndexable::FIELD_TYPE_INTEGER,
		'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
		'version' => IIndexable::FIELD_TYPE_INTEGER,
		'caption_asset_status' => IIndexable::FIELD_TYPE_INTEGER,
		'size' => IIndexable::FIELD_TYPE_INTEGER,
		'is_default' => IIndexable::FIELD_TYPE_INTEGER,
		'start_time' => IIndexable::FIELD_TYPE_INTEGER,
		'end_time' => IIndexable::FIELD_TYPE_INTEGER,
		
		'created_at' => IIndexable::FIELD_TYPE_DATETIME,
		'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
		
		'str_entry_id' => IIndexable::FIELD_TYPE_STRING,
		'str_caption_asset_id' => IIndexable::FIELD_TYPE_STRING,
	);
	
	/* (non-PHPdoc)
	 * @see IIndexable::getIndexFieldType()
	 */
	public function getIndexFieldType($field)
	{
		if(isset(self::$indexFieldTypes[$field]))
			return self::$indexFieldTypes[$field];
			
		return null;
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
