<?php


/**
 * Skeleton subclass for representing a row from the 'entry_distribution' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class EntryDistribution extends BaseEntryDistribution implements IIndexable, ISyncableFile
{
	// TODO - add file syncs for the sent XML
	
	const FILE_SYNC_ENTRY_DISTRIBUTION_SUBMIT_RESULTS = 1;
	const FILE_SYNC_ENTRY_DISTRIBUTION_UPDATE_RESULTS = 2;
	const FILE_SYNC_ENTRY_DISTRIBUTION_DELETE_RESULTS = 3;
	
	const CUSTOM_DATA_FIELD_SUBMIT_RESULTS_VERSION = "SubmitResultsVersion";
	const CUSTOM_DATA_FIELD_UPDATE_RESULTS_VERSION = "UpdateResultsVersion";
	const CUSTOM_DATA_FIELD_DELETE_RESULTS_VERSION = "DeleteResultsVersion";

	/**
	 * Get the [optionally formatted] temporal [next_report] calculated value.
	 * 
	 * @param      string $format The date/time format string (either date()-style or strftime()-style).
	 *							If format is NULL, then the raw unix timestamp integer will be returned.
	 * @return     mixed Formatted date/time value as string or (integer) unix timestamp (if format is NULL), NULL if column is NULL, and 0 if column value is 0000-00-00 00:00:00
	 * @throws     PropelException - if unable to parse/validate the date/time value.
	 */
	public function getNextReport($format = 'Y-m-d H:i:s')
	{
		if(!$this->getDistributionProfileId())
			return null;
			
		$distributionProfile = DistributionProfilePeer::retrieveByPK($this->getDistributionProfileId());
		if(!$distributionProfile || !$distributionProfile->getReportInterval() || $distributionProfile->getReportEnabled() == DistributionProfileActionStatus::DISABLED)
			return null;

		$reportInterval = $distributionProfile->getReportInterval() + (60 * 60 * 24);
		$lastReport = $this->getLastReport(null);
		$nextReport = time();
		
		if($lastReport)
			$nextReport = $lastReport + $reportInterval;
		
		if ($format === null)
			return $nextReport;
			
		if (strpos($format, '%') !== false)
			return strftime($format, $nextReport);
			
		return date($format, $nextReport);
	}
	
	/**
	 * @param int $sub_type
	 * @throws string
	 */
	private function getFileSyncVersion($sub_type)
	{
		switch($sub_type)
		{
			case self::FILE_SYNC_ENTRY_DISTRIBUTION_SUBMIT_RESULTS:
				return $this->getSubmitResultsVersion();
				
			case self::FILE_SYNC_ENTRY_DISTRIBUTION_UPDATE_RESULTS:
				return $this->getUpdateResultsVersion();
				
			case self::FILE_SYNC_ENTRY_DISTRIBUTION_DELETE_RESULTS:
				return $this->getDeleteResultsVersion();
		}
		return null;
	}
	
	/**
	 * @param int $sub_type
	 * @throws FileSyncException
	 */
	private static function validateFileSyncSubType($sub_type)
	{
		$valid_sub_types = array(
			self::FILE_SYNC_ENTRY_DISTRIBUTION_SUBMIT_RESULTS,
			self::FILE_SYNC_ENTRY_DISTRIBUTION_UPDATE_RESULTS,
			self::FILE_SYNC_ENTRY_DISTRIBUTION_DELETE_RESULTS,
		);
		
		if(! in_array($sub_type, $valid_sub_types))
			throw new FileSyncException(ContentDistributionFileSyncObjectType::GENERIC_DISTRIBUTION_ACTION, $sub_type, $valid_sub_types);
	}
	
	/* (non-PHPdoc)
	 * @see ISyncableFile::getSyncKey()
	 */
	public function getSyncKey($sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getFileSyncVersion($sub_type);
		
		$key = new FileSyncKey();
		$key->object_type = ContentDistributionFileSyncObjectType::get()->coreValue(ContentDistributionFileSyncObjectType::GENERIC_DISTRIBUTION_ACTION);
		$key->object_sub_type = $sub_type;
		$key->object_id = $this->getId();
		$key->version = $version;
		$key->partner_id = $this->getPartnerId();
		
		return $key;
	}

	/* (non-PHPdoc)
	 * @see ISyncableFile::generateFilePathArr()
	 */
	public function generateFilePathArr($sub_type, $version = null)
	{
		self::validateFileSyncSubType ( $sub_type );
		
		if(!$version)
			$version = $this->getFileSyncVersion($sub_type);
		
		$dir = (intval($this->getId() / 1000000)) . '/' . (intval($this->getId() / 1000) % 1000);
		$path =  "/content/distribution/generic/$dir/" . $this->generateFileName($sub_type, $version);

		return array(myContentStorage::getFSContentRootPath(), $path); 
	}

	/* (non-PHPdoc)
	 * @see ISyncableFile::generateFileName()
	 */
	public function generateFileName($sub_type, $version = null)
	{
		self::validateFileSyncSubType($sub_type);
		
		if(!$version)
			$version = $this->getFileSyncVersion($sub_type);
	
		$extension = 'log';
		
		return $this->getId() . "_{$sub_type}_{$version}.{$extension}";	
	}
	
	/**
	 * @var FileSync
	 */
	private $m_file_sync;

	/* (non-PHPdoc)
	 * @see ISyncableFile::getFileSync()
	 */
	public function getFileSync()
	{
		return $this->m_file_sync; 
	}

	/* (non-PHPdoc)
	 * @see ISyncableFile::setFileSync()
	 */
	public function setFileSync(FileSync $file_sync)
	{
		 $this->m_file_sync = $file_sync;
	}
	
	public function setFlavorAssetIds($v)
	{
		if(is_array($v))
			$v = implode(',', $v);
			
		return parent::setFlavorAssetIds($v);
	}
	
	public function setThumbAssetIds($v)
	{
		if(is_array($v))
			$v = implode(',', $v);
			
		return parent::setThumbAssetIds($v);
	}

	public function getValidationErrors()
	{
		$validationErrors = parent::getValidationErrors();
		if(!$validationErrors)
			return array();
	
		try{
			return unserialize($validationErrors);
		}
		catch(Exception $e){
			KalturaLog::err("Unable to unserialize [$validationErrors]");
		}
		return array();
	}

	public function setValidationErrorsArray(array $v)
	{
		return parent::setValidationErrors(serialize($v));
	}

	public function getSunStatus()
	{
		$now = time();
		if($now < $this->getSunrise(null))
			return EntryDistributionSunStatus::BEFORE_SUNRISE;
			
		if($now > $this->getSunset(null))
			return EntryDistributionSunStatus::AFTER_SUNSET;
			
		return EntryDistributionSunStatus::AFTER_SUNRISE;
	}

	/* (non-PHPdoc)
	 * @see IIndexable::getIntId()
	 */
	public function getIntId()
	{
		return $this->getId();
	}

	/* (non-PHPdoc)
	 * @see IIndexable::getObjectIndexName()
	 */
	public function getObjectIndexName()
	{
		return EntryDistributionPeer::TABLE_NAME;
	}

	/* (non-PHPdoc)
	 * @see IIndexable::getIndexFieldsMap()
	 */
	public function getIndexFieldsMap()
	{
		return array(
			'entry_distribution_id' => 'id',
			'created_at' => 'createdAt',
			'updated_at' => 'updatedAt',
			'submitted_at' => 'submittedAt',
			'entry_id' => 'entryId',
			'partner_id' => 'partnerId',
			'distribution_profile_id' => 'distributionProfileId',
			'entry_distribution_status' => 'status',
			'dirty_status' => 'dirtyStatus',
			'thumb_asset_ids' => 'thumbAssetIds',
			'flavor_asset_ids' => 'flavorAssetIds',
			'sunrise' => 'sunrise',
			'sunset' => 'sunset',
			'remote_id' => 'remoteId',
			'plays' => 'plays',
			'views' => 'views',
			'error_type' => 'errorType',
			'error_number' => 'errorNumber',
			'last_report' => 'lastReport',
			'next_report' => 'nextReport',
		);
	}

	private static $indexFieldTypes = array(
		'entry_distribution_id' => IIndexable::FIELD_TYPE_INTEGER,
		'created_at' => IIndexable::FIELD_TYPE_DATETIME,
		'updated_at' => IIndexable::FIELD_TYPE_DATETIME,
		'submitted_at' => IIndexable::FIELD_TYPE_DATETIME,
		'entry_id' => IIndexable::FIELD_TYPE_STRING,
		'partner_id' => IIndexable::FIELD_TYPE_INTEGER,
		'distribution_profile_id' => IIndexable::FIELD_TYPE_INTEGER,
		'entry_distribution_status' => IIndexable::FIELD_TYPE_INTEGER,
		'dirty_status' => IIndexable::FIELD_TYPE_INTEGER,
		'thumb_asset_ids' => IIndexable::FIELD_TYPE_STRING,
		'flavor_asset_ids' => IIndexable::FIELD_TYPE_STRING,
		'sunrise' => IIndexable::FIELD_TYPE_DATETIME,
		'sunset' => IIndexable::FIELD_TYPE_DATETIME,
		'remote_id' => IIndexable::FIELD_TYPE_STRING,
		'plays' => IIndexable::FIELD_TYPE_INTEGER,
		'views' => IIndexable::FIELD_TYPE_INTEGER,
		'error_type' => IIndexable::FIELD_TYPE_INTEGER,
		'error_number' => IIndexable::FIELD_TYPE_INTEGER,
		'last_report' => IIndexable::FIELD_TYPE_DATETIME,
		'next_report' => IIndexable::FIELD_TYPE_DATETIME,
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
	 * @see lib/model/om/BaseEntryDistribution#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		$objectUpdated = $this->isModified();
		$objectDeleted = false;
		if($this->isColumnModified(EntryDistributionPeer::STATUS) && $this->getStatus() == EntryDistributionStatus::DELETED)
			$objectDeleted = true;
			
		$ret = parent::postUpdate($con);
		
		if($objectDeleted)
			kEventsManager::raiseEvent(new kObjectDeletedEvent($this));
			
		if($objectUpdated)
			kEventsManager::raiseEvent(new kObjectUpdatedEvent($this));
			
		return $ret;
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/om/BaseEntryDistribution#postInsert()
	 */
	public function postInsert(PropelPDO $con = null)
	{
		parent::postInsert($con);
		
		kEventsManager::raiseEvent(new kObjectAddedEvent($this));
	}

	public function getSubmitResultsVersion()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_SUBMIT_RESULTS_VERSION);}
	public function getUpdateResultsVersion()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_UPDATE_RESULTS_VERSION);}
	public function getDeleteResultsVersion()			{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_DELETE_RESULTS_VERSION);}

	public function incrementSubmitResultsVersion()		{return $this->incInCustomData(self::CUSTOM_DATA_FIELD_SUBMIT_RESULTS_VERSION);}
	public function incrementUpdateResultsVersion()		{return $this->incInCustomData(self::CUSTOM_DATA_FIELD_UPDATE_RESULTS_VERSION);}
	public function incrementDeleteResultsVersion()		{return $this->incInCustomData(self::CUSTOM_DATA_FIELD_DELETE_RESULTS_VERSION);}
	
} // EntryDistribution
