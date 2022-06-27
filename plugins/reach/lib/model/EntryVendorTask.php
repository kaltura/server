<?php


/**
 * Skeleton subclass for representing a row from the 'entry_vendor_task' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package plugins.reach
 * @subpackage model
 */
class EntryVendorTask extends BaseEntryVendorTask implements IRelatedObject, IIndexable
{
	const CUSTOM_DATA_NOTES = 				'notes';
	const CUSTOM_DATA_ACCESS_KEY = 			'access_key';
	const CUSTOM_DATA_ERR_DESCRIPTION = 	'err_description';
	const CUSTOM_DATA_USER_ID = 			'user_id';
	const CUSTOM_DATA_MODERATING_USER = 	'moderating_user';
	const CUSTOM_DATA_ACCURACY 	= 			'accuracy';
	const CUSTOM_DATA_OUTPUT_OBJECT_ID = 	'output_object_id';
	const CUSTOM_DATA_DICTIONARY =          'dictionary';
	const CUSTOM_DATA_PARTNER_DATA =        'partner_data';
	const CUSTOM_DATA_CREATION_MODE =       'creation_mode';
	const CUSTOM_DATA_IS_REQUEST_MODERATED ='request_moderated';
	const CUSTOM_DATA_IS_OUTPUT_MODERATED = 'output_moderated';
	const CUSTOM_DATA_ACCESS_KEY_EXPIRY =   'access_key_expiry';
	const CUSTOM_DATA_TASK_DATA =       	'task_data';
	const CUSTOM_DATA_OLD_PRICE =       	'old_price';
	const CUSTOM_DATA_EXPECTED_FINISH_TIME ='expectedFinishTime';
	const CUSTOM_DATA_SERVICE_TYPE =    	'serviceType';
	const CUSTOM_DATA_SERVICE_FEATURE = 	'serviceFeature';
	const CUSTOM_DATA_TURN_AROUND_TIME =	'turnAroundTime';
	const CUSTOM_DATA_EXTERNAL_TASK_ID =      'externalTaskId';
	const SEVEN_DAYS =			 604800;
	const BUSINESS_DAY_FRIDAY = 5;      //Monday is 1
	const BUSINESS_DAY_NEXT_MONDAY = 8;
	const BUSINESS_DAY_START_HOUR = 6;  //06:00:00
	const BUSINESS_DAY_END_HOUR = 18;   //18:00:00
	const BUSINESS_DAY_TIME_NORMALIZATION_FACTOR = 10000;

	//setters
	
	public function setNotes($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_NOTES, $v);
	}

	public function setDictionary($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_DICTIONARY, $v);
	}

	public function setAccessKey($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ACCESS_KEY, $v);
	}
	
	public function setErrDescription($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ERR_DESCRIPTION, $v);
	}
	
	public function setModeratingUser($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_MODERATING_USER, $v);
	}
	
	public function setUserId($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_USER_ID, $v);
	}
	
	public function setAccuracy($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ACCURACY, $v);
	}
	
	public function setOutputObjectId($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_OUTPUT_OBJECT_ID, $v);
	}
	
	public function setPartnerData($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_PARTNER_DATA, $v);
	}
	
	public function setCreationMode($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_CREATION_MODE, $v);
	}
	
	public function setIsRequestModerated($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_IS_REQUEST_MODERATED, $v);
	}
	
	public function setIsOutputModerated($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_IS_OUTPUT_MODERATED, $v);
	}
	
	public function setAccessKeyExpiry($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_ACCESS_KEY_EXPIRY, $v);
	}
	
	public function setTaskJobData($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_TASK_DATA, $v);
	}
	
	public function setOldPrice($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_OLD_PRICE, $v);
	}

	public function setExpectedFinishTime($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_EXPECTED_FINISH_TIME, $v);
	}

	public function setServiceType($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_SERVICE_TYPE, $v);
	}

	public function setServiceFeature($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_SERVICE_FEATURE, $v);
	}

	public function setTurnAroundTime($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_TURN_AROUND_TIME, $v);
	}

	public function setExternalTaskId($v)
	{
		$this->putInCustomData(self::CUSTOM_DATA_EXTERNAL_TASK_ID, $v);
	}


	//getters

	public function getDictionary()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_DICTIONARY, null, null);
	}

	public function getNotes()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_NOTES, null, null);
	}
	
	public function getAccessKey()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ACCESS_KEY, null, null);
	}
	
	public function getErrDescription()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ERR_DESCRIPTION, null, null);
	}
	
	public function getModeratingUser()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_MODERATING_USER, null, null);
	}
	
	public function getUserId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_USER_ID, null, null);
	}
	
	public function getReachProfile()
	{
		return ReachProfilePeer::retrieveByPK($this->getReachProfileId());
	}
	
	public function getEntry()
	{
		return entryPeer::retrieveByPK($this->getEntryId());
	}
	
	public function getCatalogItem()
	{
		return VendorCatalogItemPeer::retrieveByPKNoFilter($this->getCatalogItemId());
	}
	
	public function getAccuracy()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ACCURACY, null, null);
	}
	
	public function getOutputObjectId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_OUTPUT_OBJECT_ID, null, null);
	}
	
	public function getPartnerData()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_PARTNER_DATA, null, null);
	}
	
	public function getCreationMode()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_CREATION_MODE, null, EntryVendorTaskCreationMode::MANUAL);
	}
	
	public function getIsRequestModerated()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_IS_REQUEST_MODERATED, null, null);
	}
	
	public function getIsOutputModerated()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_IS_OUTPUT_MODERATED, null, false);
	}
	
	public function getAccessKeyExpiry()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_ACCESS_KEY_EXPIRY, null, dateUtils::DAY * 7);
	}
	
	public function getTaskJobData()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_TASK_DATA);
	}
	
	public function getOldPrice()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_OLD_PRICE);
	}

	public function getExpectedFinishTime()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_EXPECTED_FINISH_TIME);
	}

	public function getServiceType()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SERVICE_TYPE);
	}

	public function getServiceFeature()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_SERVICE_FEATURE);
	}

	public function getTurnAroundTime()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_TURN_AROUND_TIME);
	}

	public function getExternalTaskId()
	{
		return $this->getFromCustomData(self::CUSTOM_DATA_EXTERNAL_TASK_ID);
	}

	protected static function calculateSecondsToNextBusinessDay($currentDateTime, $currentDay, $currentTime)
	{

		$endDayString = sprintf("Y-m-d %s:00:00", self::BUSINESS_DAY_END_HOUR);

		if( $currentDay > self::BUSINESS_DAY_FRIDAY )
		{
			//Weekend
			$daysToSkip = self::BUSINESS_DAY_NEXT_MONDAY - $currentDay;
			$expTime = date($endDayString, strtotime("+$daysToSkip days", $currentDateTime));
		}
		else
		{
			$endDayTime = self::BUSINESS_DAY_END_HOUR * self::BUSINESS_DAY_TIME_NORMALIZATION_FACTOR;
			$startDayTime = self::BUSINESS_DAY_START_HOUR * self::BUSINESS_DAY_TIME_NORMALIZATION_FACTOR;

			//Middle of a business day - expiration time: add 24 hours
			if( ($startDayTime <= $currentTime) && ($currentTime <= $endDayTime) )
			{
				$expTime = date("Y-m-d H:i:s", strtotime('+1 days', $currentDateTime));
			}
			//Before work hours - expiration time: End Day today
			elseif ($currentTime < $startDayTime)
			{
				$expTime = date($endDayString, $currentDateTime);
			}
			//After work hours - expiration time: End Day tomorrow
			else
			{
				$expTime = date($endDayString, strtotime('+1 days', $currentDateTime));
			}

			//if expiration time falls on the weekend, jump 2 days
			$expTimeDay = date("N", strtotime($expTime));
			if ($expTimeDay > self::BUSINESS_DAY_FRIDAY)
			{
				$expTime = date("Y-m-d H:i:s", strtotime($expTime . ' +2 days'));
			}
		}

		return strtotime($expTime) - $currentDateTime;
	}

	public static function calculateBusinessDays($numBusinessDays, $currentUnixTime)
	{
		//Get Local TZ
		$localTimeZone = date_default_timezone_get();

		//Set to EST Time
		date_default_timezone_set('America/New_York');

		$turnAroundTime = 0;

		//Calculate the business days
		do
		{
			$expTime = $turnAroundTime + $currentUnixTime;

			$day = date("N", $expTime);
			$time = date("His", $expTime);

			$turnAroundTime += self::calculateSecondsToNextBusinessDay($expTime, $day, $time);
			$numBusinessDays--;

		}while($numBusinessDays);

		//Restore
		date_default_timezone_set($localTimeZone);

		KalturaLog::info("Seconds To Expiration Time: $turnAroundTime");

		return $turnAroundTime;
	}

/* (non-PHPdoc)
  * @see BaseEntryVendorTask::preSave()
  */
	public function preSave(PropelPDO $con = null)
	{
		if ($this->isColumnModified(EntryVendorTaskPeer::STATUS) && $this->getStatus() == EntryVendorTaskStatus::PENDING)
		{
			$time = time();
			$this->setQueueTime($time);
			$vendorCatalogItem = VendorCatalogItemPeer::retrieveByPK($this->getCatalogItemId());
			if ($vendorCatalogItem)
			{
				$turnAroundTime = $vendorCatalogItem->getTurnAroundTime();
				if ($turnAroundTime == VendorServiceTurnAroundTime::BEST_EFFORT)
				{
					$turnAroundTime = self::SEVEN_DAYS;
				}
				elseif( (VendorServiceTurnAroundTime::ONE_BUSINESS_DAY <= $turnAroundTime) &&
					($turnAroundTime <= VendorServiceTurnAroundTime::SEVEN_BUSINESS_DAYS) )
				{
					$turnAroundTime = self::calculateBusinessDays($turnAroundTime, $time);
				}

				$this->setExpectedFinishTime($turnAroundTime + $time);
			}
		}
		
		if ($this->isColumnModified(EntryVendorTaskPeer::STATUS) && in_array($this->getStatus(), array(EntryVendorTaskStatus::READY, EntryVendorTaskStatus::ERROR)))
		{
			$this->setFinishTime(time());
		}
		
		/*
		$entryDuration = $this->getEntry()->getLengthInMsecs();
		$taskJobData = $this->getTaskJobData();
		if(!$taskJobData)
		{
			$taskJobData = new kVendorTaskData();
		}
		$taskJobData->setEntryDuration($entryDuration);
		$this->setTaskJobData($taskJobData);
		*/
		
		return parent::preSave($con);
	}
	
	public function getKuser()
	{
		return kuserPeer::retrieveByPk($this->kuser_id);
	}
	
	// IIndexable interface implantation 
	
	/**
	 * Is the id as used and know by the indexing server
	 * @return int
	 */
	public function getIntId()
	{
		return $this->getId();
	}
	
	/**
	 * @return string
	 */
	public function getEntryId()
	{
		return parent::getEntryId();
	}
	
	/**
	 * This function returns the index object name (the one responsible for the sphinx mapping)
	 */
	public function getIndexObjectName() 
	{
		return "EntryVendorTaskIndex";
	}
	
	/**
	 * @param int $time
	 * @return IIndexable
	 */
	public function setUpdatedAt($time)
	{
		parent::setUpdatedAt($time);
		if(!in_array(entryPeer::UPDATED_AT, $this->modifiedColumns, false))
			$this->modifiedColumns[] = entryPeer::UPDATED_AT;
		
		return $this;
	}
	
	/**
	 * Index the object in the search engine
	 */
	public function indexToSearchIndex()
	{
		kEventsManager::raiseEventDeferred(new kObjectReadyForIndexEvent($this));
	}
	
	public function getSphinxIndexName()
	{
		return kSphinxSearchManager::getSphinxIndexName(EntryVendorTaskIndex::getObjectIndexName());
	}
	
	public function postSave(PropelPDO $con = null)
	{
		parent::postSave($con);
		$this->indexToSearchIndex();
	}

	public function save(PropelPDO $con = null)
	{
		if (in_array(EntryVendorTaskPeer::STATUS, $this->modifiedColumns) &&
			(in_array($this->status, array(EntryVendorTaskStatus::PROCESSING, EntryVendorTaskStatus::ABORTED))))
		{
			if (in_array($this->oldColumnsValues[EntryVendorTaskPeer::STATUS],
				array(EntryVendorTaskStatus::PENDING, EntryVendorTaskStatus::PENDING_MODERATION, EntryVendorTaskStatus::PENDING_ENTRY_READY)))
			{
				return parent::save($con);
			}
			else
			{
				throw new kCoreException("Entry vendor task item with id [$this->id] could not be updated to status [$this->status]", kCoreException::ENTRY_VENDOR_TASK_ITEM_COULD_NOT_BE_UPDATED);

			}
		}
		parent::save($con);
	}

} // EntryVendorTask
