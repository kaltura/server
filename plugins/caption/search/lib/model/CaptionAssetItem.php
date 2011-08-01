<?php
/**
 * @package plugins.captionSearch
 * @subpackage model
 */ 
class CaptionAssetItem extends BaseObject implements IIndexable
{
	/**
	 * @var CaptionAsset
	 */
	protected $asset;
	
	/**
	 * @var int
	 */
	protected $startTime;
	
	/**
	 * @var int
	 */
	protected $endTime;
	
	/**
	 * 
	 * @var string
	 */
	protected $content;
	
	/**
	 * @param CaptionAsset $asset
	 * @param int $startTime
	 * @param int $endTime
	 * @param string $content
	 */
	public function __construct(CaptionAsset $asset, $startTime, $endTime, $content)
	{
		$this->asset = $asset;
		$this->startTime = $startTime;
		$this->endTime = $endTime;
		$this->content = $content;
	}
	
	/**
	 * @return CaptionAsset
	 */
	public function getAsset()
	{
		return $this->asset();
	}
	
	/**
	 * @return entry
	 */
	public function getEntry()
	{
		return $this->asset->getentry();
	}
	
	/* (non-PHPdoc)
	 * @see IIndexable::getId()
	 */
	public function getId()
	{
		return $this->asset->getId();
	}

	/* (non-PHPdoc)
	 * @see IIndexable::getIntId()
	 */
	public function getIntId()
	{
		return $this->asset->getIntId();
	}

	/* (non-PHPdoc)
	 * @see IIndexable::getEntryId()
	 */
	public function getEntryId()
	{
		return $this->asset->getEntryId();
	}
	
	/**
	 * @return string
	 */
	public function getTags()
	{
		return $this->asset->getTags();
	}
	
	/**
	 * @return string
	 */
	public function getContent()
	{
		return $this->content;
	}
	
	/**
	 * @return string
	 */
	public function getPartnerDescription()
	{
		return $this->asset->getPartnerDescription();
	}
	
	/**
	 * @return string
	 */
	public function getLanguage()
	{
		return $this->asset->getLanguage();
	}
	
	/**
	 * @return string
	 */
	public function getLabel()
	{
		return $this->asset->getLabel();
	}
	
	/**
	 * @return string
	 */
	public function getFormat()
	{
		return $this->asset->getContainerFormat();
	}
	
	/**
	 * @return int
	 */
	public function getCaptionParamsId()
	{
		return $this->asset->getFlavorParamsId();
	}
	
	/**
	 * @return int
	 */
	public function getVersion()
	{
		return $this->asset->getVersion();
	}
	
	/**
	 * @return int
	 */
	public function getStatus()
	{
		return $this->asset->getStatus();
	}
	
	/**
	 * @return int
	 */
	public function getSize()
	{
		return $this->asset->getSize();
	}
	
	/**
	 * @return int
	 */
	public function getDefault()
	{
		return $this->asset->getDefault();
	}
	
	/**
	 * @return int
	 */
	public function getStartTime()
	{
		return $this->startTime;
	}
	
	/**
	 * @return int
	 */
	public function getEndTime()
	{
		return $this->endTime;
	}
	
	/**
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getCreatedAt($format = 'Y-m-d H:i:s')
	{
		return $this->asset->getCreatedAt($format);
	}
	
	/**
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getUpdatedAt($format = 'Y-m-d H:i:s')
	{
		return $this->asset->getUpdatedAt($format);
	}
	
	/**
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getDeletedAt($format = 'Y-m-d H:i:s')
	{
		return $this->asset->getDeletedAt($format);
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
			'caption_asset_id' => 'id',
			'tags' => 'tags',
			'content' => 'content',
			'partner_description' => 'partnerDescription',
			'language' => 'language',
			'label' => 'label',
			'format' => 'format',
			
			'int_caption_asset_id' => 'intId',
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
			'deleted_at' => 'deletedAt',
			
			'str_entry_id' => 'entryId',
			'str_caption_asset_id' => 'id',
			'str_content' => 'content',
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
		
		'int_caption_asset_id' => IIndexable::FIELD_TYPE_INTEGER,
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
		'deleted_at' => IIndexable::FIELD_TYPE_DATETIME,
		
		'str_entry_id' => IIndexable::FIELD_TYPE_STRING,
		'str_caption_asset_id' => IIndexable::FIELD_TYPE_STRING,
		'str_content' => IIndexable::FIELD_TYPE_STRING,
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
		return $this->asset->setUpdatedAt($time);
	}

	/* (non-PHPdoc)
	 * @see IBaseObject::getPartnerId()
	 */
	public function getPartnerId()
	{
		return $this->asset->getPartnerId();
	}

	/**
	 * Save the object to the index
	 */
	public function index()
	{
		kEventsManager::raiseEvent(new kObjectAddedEvent($this));
	}
}