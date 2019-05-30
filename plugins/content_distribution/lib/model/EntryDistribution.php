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
 * @package plugins.contentDistribution
 * @subpackage model
 */
class EntryDistribution extends BaseEntryDistribution implements IIndexable, ISyncableFile, IRelatedObject
{
	const FILE_SYNC_ENTRY_DISTRIBUTION_SUBMIT_RESULTS = 1;
	const FILE_SYNC_ENTRY_DISTRIBUTION_UPDATE_RESULTS = 2;
	const FILE_SYNC_ENTRY_DISTRIBUTION_DELETE_RESULTS = 3;
	const FILE_SYNC_ENTRY_DISTRIBUTION_SUBMIT_DATA = 4;
	const FILE_SYNC_ENTRY_DISTRIBUTION_UPDATE_DATA = 5;
	const FILE_SYNC_ENTRY_DISTRIBUTION_DELETE_DATA = 6;
	
	const CUSTOM_DATA_FIELD_MEDIA_FILES = "MediaFiles";
	const CUSTOM_DATA_FIELD_SUBMIT_RESULTS_VERSION = "SubmitResultsVersion";
	const CUSTOM_DATA_FIELD_UPDATE_RESULTS_VERSION = "UpdateResultsVersion";
	const CUSTOM_DATA_FIELD_DELETE_RESULTS_VERSION = "DeleteResultsVersion";
	const CUSTOM_DATA_FIELD_SUBMIT_DATA_VERSION = "SubmitDataVersion";
	const CUSTOM_DATA_FIELD_UPDATE_DATA_VERSION = "UpdateDataVersion";
	const CUSTOM_DATA_FIELD_DELETE_DATA_VERSION = "DeleteDataVersion";
	
	public function getIndexObjectName() {
		return "EntryDistributionIndex";
	}
	
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
		if(!$distributionProfile || $distributionProfile->getStatus() != DistributionProfileStatus::ENABLED || !$distributionProfile->getReportInterval() || $distributionProfile->getReportEnabled() == DistributionProfileActionStatus::DISABLED)
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
	 * @return entry
	 */
	protected function getEntry()
	{
		$entryId = $this->getEntryId();
		$entry = entryPeer::getInstanceFromPool($entryId);
		if(!$entry)
			$entry = entryPeer::retrieveByPKNoFilter($entryId);
			
		return $entry; 
	}
	
	/* (non-PHPdoc)
	 * @see BaseEntryDistribution::getSunrise()
	 */
	public function getSunrise($format = 'Y-m-d H:i:s')
	{
		$sunrise = parent::getSunrise($format);
		if(!is_null($sunrise))
			return $sunrise;
			
		$entry = $this->getEntry();
		if(!$entry)
			return null;
				
		$sunrise = $entry->getStartDate($format);
		if(!is_null($sunrise))
			return $sunrise;
			
		return $entry->getCreatedAt($format);
	}
	
	/* (non-PHPdoc)
	 * @see BaseEntryDistribution::getSunset()
	 */
	public function getSunset($format = 'Y-m-d H:i:s')
	{
		$sunset = parent::getSunset($format);
		if(!is_null($sunset))
			return $sunset;
			
		$entry = $this->getEntry();
		if(!$entry)
			return null;
				
		return $entry->getEndDate($format);
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
				
			case self::FILE_SYNC_ENTRY_DISTRIBUTION_SUBMIT_DATA:
				return $this->getSubmitDataVersion();
				
			case self::FILE_SYNC_ENTRY_DISTRIBUTION_UPDATE_DATA:
				return $this->getUpdateDataVersion();
				
			case self::FILE_SYNC_ENTRY_DISTRIBUTION_DELETE_DATA:
				return $this->getDeleteDataVersion();
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
			self::FILE_SYNC_ENTRY_DISTRIBUTION_SUBMIT_DATA,
			self::FILE_SYNC_ENTRY_DISTRIBUTION_UPDATE_DATA,
			self::FILE_SYNC_ENTRY_DISTRIBUTION_DELETE_DATA,
		);
		
		if(! in_array($sub_type, $valid_sub_types))
			throw new FileSyncException(ContentDistributionFileSyncObjectType::ENTRY_DISTRIBUTION, $sub_type, $valid_sub_types);
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
		$key->object_type = ContentDistributionPlugin::getContentDistributionFileSyncObjectTypeCoreValue(ContentDistributionFileSyncObjectType::ENTRY_DISTRIBUTION);
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
		{
			$v = array_unique($v);
			sort($v); // the sort is importanet to idetify changes, when the list is changed the update required flag is raised
			$v = implode(',', $v);
		}
		return parent::setFlavorAssetIds($v);
	}

	public function setThumbAssetIds($v)
	{
		if(is_array($v))
		{
			$v = array_unique($v);
			sort($v); // the sort is importanet to idetify changes, when the list is changed the update required flag is raised
			$v = implode(',', $v);
		}
			
		return parent::setThumbAssetIds($v);
	}
	
	public function setAssetIds($v)
	{
		if(is_array($v))
		{
			$v = array_unique($v);
			sort($v); // the sort is importanet to idetify changes, when the list is changed the update required flag is raised
			$v = implode(',', $v);
		}
			
		return parent::setAssetIds($v);
	}

	/**
	 * @return array<kDistributionValidationError>
	 * @see BaseEntryDistribution::getValidationErrors()
	 */
	public function getValidationErrors()
	{
		$validationErrors = parent::getValidationErrors();
		if(!$validationErrors)
			return array();
	
		try{
			$arr = unserialize($validationErrors);
			if(is_array($arr))
				return $arr;
				
			KalturaLog::err("Unable to unserialize [$validationErrors]");
			return array();
		}
		catch(Exception $e){
			KalturaLog::err("Unable to unserialize [$validationErrors]");
		}
		return array();
	}

	/**
	 * @param array<kDistributionValidationError> $v
	 * @return EntryDistribution
	 */
	public function setValidationErrorsArray(array $v)
	{
		return parent::setValidationErrors(serialize($v));
	}

	public function getSunStatus()
	{		
		$now = time();
		if($this->getSunrise(null) && $now < $this->getSunrise(null))
			return EntryDistributionSunStatus::BEFORE_SUNRISE;
			
		if($this->getSunset(null) && $now > $this->getSunset(null))
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
	 * @see IIndexable::indexToSearchIndex()
	 */
	public function indexToSearchIndex()
	{
		kEventsManager::raiseEventDeferred(new kObjectReadyForIndexEvent($this));
	}
	
	public function getSphinxIndexName()
	{
		return kSphinxSearchManager::getSphinxIndexName(EntryDistributionIndex::getObjectIndexName());
	}
	
	/* (non-PHPdoc)
	 * @see lib/model/om/BaseEntryDistribution#postUpdate()
	 */
	public function postUpdate(PropelPDO $con = null)
	{
		if ($this->alreadyInSave)
			return parent::postUpdate($con);
		
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
	public function getSubmitDataVersion()				{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_SUBMIT_DATA_VERSION);}
	public function getUpdateDataVersion()				{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_UPDATE_DATA_VERSION);}
	public function getDeleteDataVersion()				{return $this->getFromCustomData(self::CUSTOM_DATA_FIELD_DELETE_DATA_VERSION);}
	
	public function incrementSubmitResultsVersion()
	{
		$version = kDataCenterMgr::incrementVersion($this->getSubmitResultsVersion());
		return $this->putInCustomData(self::CUSTOM_DATA_FIELD_SUBMIT_RESULTS_VERSION, $version);
	}
	
	public function incrementUpdateResultsVersion()
	{
		$version = kDataCenterMgr::incrementVersion($this->getUpdateResultsVersion());
		return $this->putInCustomData(self::CUSTOM_DATA_FIELD_UPDATE_RESULTS_VERSION, $version);
	}
	
	public function incrementDeleteResultsVersion()
	{
		$version = kDataCenterMgr::incrementVersion($this->getDeleteResultsVersion());
		return $this->putInCustomData(self::CUSTOM_DATA_FIELD_DELETE_RESULTS_VERSION, $version);
	}
	
	public function incrementSubmitDataVersion()
	{
		$version = kDataCenterMgr::incrementVersion($this->getSubmitDataVersion());
		return $this->putInCustomData(self::CUSTOM_DATA_FIELD_SUBMIT_DATA_VERSION, $version);
	}
	
	public function incrementUpdateDataVersion()
	{
		$version = kDataCenterMgr::incrementVersion($this->getUpdateDataVersion());
		return $this->putInCustomData(self::CUSTOM_DATA_FIELD_UPDATE_DATA_VERSION, $version);
	}
	
	public function incrementDeleteDataVersion()
	{
		$version = kDataCenterMgr::incrementVersion($this->getDeleteDataVersion());
		return $this->putInCustomData(self::CUSTOM_DATA_FIELD_DELETE_DATA_VERSION, $version);
	}

	
	/**
	 * @return array $mediaFiles
	 */
	public function getMediaFiles()
	{
		$mediaFiles = $this->getFromCustomData(self::CUSTOM_DATA_FIELD_MEDIA_FILES);
		if(!$mediaFiles || !is_array($mediaFiles))
			return array();
			
		return $mediaFiles;
	}

	/**
	 * @param array<kDistributionRemoteMediaFile> $mediaFiles
	 */
	public function setMediaFiles(array $mediaFiles)
	{
		$this->putInCustomData(self::CUSTOM_DATA_FIELD_MEDIA_FILES, $mediaFiles);
		return $this;
	}
	public function getCacheInvalidationKeys()
	{
		return array("entryDistribution:entryId=".strtolower($this->getEntryId()));
	}

} // EntryDistribution
