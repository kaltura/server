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
	const CUSTOM_DATA_SERVICE_TYPE =	'serviceType';
	const CUSTOM_DATA_SERVICE_FEATURE =	'serviceFeature';
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

	protected function calculateNextBusinessDay()
	{
		$currentDay = date("N");

		$endDayString = sprintf("Y-m-d %s:00:00", self::BUSINESS_DAY_END_HOUR);

		if( $currentDay > self::BUSINESS_DAY_FRIDAY )
		{
			//Weekend
			$daysToSkip = self::BUSINESS_DAY_NEXT_MONDAY - $currentDay;
			$expTime = date($endDayString, strtotime("+$daysToSkip days"));
		}
		else
		{
			$now = date("His");

			$endDayTime = self::BUSINESS_DAY_END_HOUR * self::BUSINESS_DAY_TIME_NORMALIZATION_FACTOR;
			$startDayTime = self::BUSINESS_DAY_START_HOUR * self::BUSINESS_DAY_TIME_NORMALIZATION_FACTOR;

			//Middle of a business day - expiration time: add 24 hours
			if( ($startDayTime <= $now) && ($now <= $endDayTime) )
			{
				$expTime = date("Y-m-d H:i:s", strtotime('+1 days'));
			}
			//Before work hours - expiration time: End Day today
			elseif ($now < $startDayTime)
			{
				$expTime = date($endDayString, strtotime('now'));
			}
			//After work hours - expiration time: End Day tomorrow
			else
			{
				$expTime = date($endDayString, strtotime('+1 days'));
			}
		}

		return $expTime;
	}

	protected function calculateBusinessDays($numBusinessDays)
	{
		//Get Local TZ
		$localTimeZone = date_default_timezone_get();

		//Set to EST Time
		date_default_timezone_set('America/New_York');
		$currentDateTime = new DateTime("now");

		//Calculate the next business day
		$expTime = $this->calculateNextBusinessDay();

		//Calculate the second business day
		if ($numBusinessDays == 2)
		{
			$expTime = date("Y-m-d H:i:s", strtotime($expTime . ' +1 days'));
		}

		//if expiration time falls on the weekend, jump 2 days
		$expTimeDay = date("N", strtotime($expTime));
		if ($expTimeDay > self::BUSINESS_DAY_FRIDAY)
		{
			$expTime = date("Y-m-d H:i:s", strtotime($expTime . ' +2 days'));
		}

		//Restore
		date_default_timezone_set($localTimeZone);

		KalturaLog::info("Expiration Time Is: " . $expTime);

		//Convert to seconds
		$diff = date_diff(date_create($expTime), $currentDateTime);
		return ($diff->d * 3600 * 24) + ($diff->h * 3600) + ($diff->i * 60) + $diff->s + 1;
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
				elseif( ($turnAroundTime == VendorServiceTurnAroundTime::ONE_BUSINESS_DAY) ||
					($turnAroundTime == VendorServiceTurnAroundTime::TWO_BUSINESS_DAYS) )
				{
					$turnAroundTime = $this->calculateBusinessDays($turnAroundTime);
				}
				$this->setExpectedFinishTime($turnAroundTime + $time);
			}
		}
		
		if ($this->isColumnModified(EntryVendorTaskPeer::STATUS) && in_array($this->getStatus(), array(EntryVendorTaskStatus::READY, EntryVendorTaskStatus::ERROR)))
		{
			$this->setFinishTime(time());
		}
		
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

} // EntryVendorTask
