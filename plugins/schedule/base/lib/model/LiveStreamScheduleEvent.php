<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class LiveStreamScheduleEvent extends BaseLiveStreamScheduleEvent implements ILiveStreamScheduleEvent
{
	const PROJECTED_AUDIENCE = 'projected_audience';
	const PRE_START_TIME = 'pre_start_time';
	const POST_END_TIME = 'post_end_time';
	const SCREENING_START_TIME = 'screening_start_time';
	const SCREENING_END_TIME = 'screening_end_time';
	const SOURCE_ENTRY_ID = 'source_entry_id';
	const PRE_START_ENTRY_ID = 'pre_start_entry_id';
	const POST_END_ENTRY_ID = 'post_end_entry_id';
	const IS_CONTENT_INTERRUPTIBLE = 'is_content_interruptible';
	const FEATURES_ARRAY = 'live_features';
	
	/**
	 * @param string $v
	 */
	public function setSourceEntryId($v)
	{
		$this->putInCustomData(self::SOURCE_ENTRY_ID, $v);
	}
	
	/**
	 * @return string
	 */
	public function getSourceEntryId()
	{
		return $this->getFromCustomData(self::SOURCE_ENTRY_ID);
	}
	
	/**
	 * @param string $v
	 */
	public function setPreStartEntryId($v)
	{
		$this->putInCustomData(self::PRE_START_ENTRY_ID, $v);
	}

	public function getPreStartEntryId()
	{
		return $this->getFromCustomData(self::PRE_START_ENTRY_ID);
	}

	/**
	 * @param string $v
	 */
	public function setPostEndEntryId($v)
	{
		$this->putInCustomData(self::POST_END_ENTRY_ID, $v);
	}

	public function getPostEndEntryId()
	{
		return $this->getFromCustomData(self::POST_END_ENTRY_ID);
	}

	/**
	 * @param int $v
	 */
	public function setProjectedAudience($v)
	{
		$this->putInCustomData(self::PROJECTED_AUDIENCE, $v);
	}

	/**
	 * @return int
	 */
	public function getProjectedAudience()
	{
		return $this->getFromCustomData(self::PROJECTED_AUDIENCE);
	}
	
	/**
	 * @param int $v
	 */
	public function setPreStartTime($v)
	{
		$this->putInCustomData(self::PRE_START_TIME, $v);
	}

	/**
	 * @return int
	 */
	public function getPreStartTime()
	{
		return $this->getFromCustomData(self::PRE_START_TIME, null, 0);
	}

	/**
	 * @param int $v
	 */
	public function setPostEndTime($v)
	{
		$this->putInCustomData(self::POST_END_TIME, $v);
	}

	/**
	 * @param array<LiveFeature> $v
	 */
	public function setLiveFeatures($v)
	{
		$serializedFeatures = serialize($v);
		$this->putInCustomData(self::FEATURES_ARRAY, $serializedFeatures);
	}

	/**
	 * @return array<LiveFeature>
	 */
	public function getLiveFeatures()
	{
		$serializedFeatures = $this->getFromCustomData(self::FEATURES_ARRAY);
		return unserialize($serializedFeatures);
	}
	
	public function shiftEvent ($parentEndDate)
	{
		$newStartDate = $parentEndDate + $this->getLinkedTo()->offset;
		$this->setStartScreenTime($newStartDate);
		$this->setEndScreenTime($newStartDate + $this->duration);
		parent::shiftEvent($parentEndDate);
	}
	
	/**
	 * @return int
	 */
	public function getPostEndTime()
	{
		return $this->getFromCustomData(self::POST_END_TIME, null, 0);
	}
	
	public function getEndScreenTime()
	{
		//For backwards compatibility
		if (is_null($this->getFromCustomData(self::SCREENING_END_TIME)))
		{
			return $this->getEndDate(null);
		}
		return $this->getFromCustomData(self::SCREENING_END_TIME);
	}
	
	public function setEndScreenTime($v)
	{
		$this->putInCustomData (self::SCREENING_END_TIME, $v);
	}
	
	public function getStartScreenTime()
	{
		//For backwards compatibility
		if (is_null($this->getFromCustomData(self::SCREENING_START_TIME)))
		{
			return $this->getStartDate(null);
		}
		return $this->getFromCustomData(self::SCREENING_START_TIME);
	}
	
	public function getCalculatedStartTime()
	{
		//For backwards compatibility
		if (is_null($this->getFromCustomData(self::SCREENING_START_TIME)))
		{
			return $this->getStartDate(null) - $this->getPreStartTime();
		}
		return parent::getCalculatedStartTime();
	}
	
	public function getCalculatedEndTime()
	{
		//For backwards compatibility
		if (is_null($this->getFromCustomData(self::SCREENING_END_TIME)))
		{
			return $this->getEndDate(null) + $this->getPostEndTime();
		}
		return parent::getCalculatedEndTime();
	}
	
	public function setStartScreenTime($v)
	{
		$this->putInCustomData(self::SCREENING_START_TIME, $v);
	}
	
	
	public function dynamicGetter($context, &$output)
	{
		$output = null;
		
		switch ($context)
		{
			case 'getLiveStatus':
				if ($this->getSourceEntryId() && ($this->getCalculatedStartTime() + kSimuliveUtils::MINIMUM_TIME_TO_PLAYABLE_SEC <= time()))
				{
					// Simulive flow (and event is playable)
					$output = EntryServerNodeStatus::PLAYABLE;
					return true;
				}
			default:
				return false;
		}
	}
	
	protected function addCapabilityToTemplateEntry($con)
	{
		$liveEntry = entryPeer::retrieveByPK($this->getTemplateEntryId());
		if ($liveEntry)
		{
			$shouldSave = false;
			if (!$liveEntry->hasCapability(LiveEntry::LIVE_SCHEDULE_CAPABILITY))
			{
				$liveEntry->addCapability(LiveEntry::LIVE_SCHEDULE_CAPABILITY);
				$shouldSave = true;
			}
			if ($this->getSourceEntryId() && !$liveEntry->hasCapability(LiveEntry::SIMULIVE_CAPABILITY))
			{
				$liveEntry->addCapability(LiveEntry::SIMULIVE_CAPABILITY);
				$shouldSave = true;
			}
			if ($shouldSave)
			{
				$liveEntry->save($con);
			}
		}
	}
	
	/* (non-PHPdoc)
	 * @see ScheduleEvent::applyDefaultValues()
	 */
	public function applyDefaultValues()
	{
		parent::applyDefaultValues();
		$this->setType(ScheduleEventType::LIVE_STREAM);
	}
	
	public function preSave(PropelPDO $con = null)
	{
		if($this->getRecurrenceType() != ScheduleEventRecurrenceType::RECURRING)
		{
			$this->setDuration($this->getEndScreenTime() - $this->getStartScreenTime());
		}
		
		$this->setCustomDataObj();
		return true;
	}

	/**
	 * @param bool $v
	 */
	public function setIsContentInterruptible($v)
	{
		$this->putInCustomData(self::IS_CONTENT_INTERRUPTIBLE, $v);
	}

	public function getIsContentInterruptible()
	{
		return $this->getFromCustomData(self::IS_CONTENT_INTERRUPTIBLE);
	}

	protected function isInsideContent()
	{
		$now = time();
		return $now > $this->getStartScreenTime() && $now < $this->getEndScreenTime();
	}

	public function isInterruptibleNow()
	{
		return $this->getIsContentInterruptible() || !$this->isInsideContent();
	}

	public function getEventTransitionTimes()
	{
		$transitionTimes = array();
		$startScreenTime = $this->getStartScreenTime();
		if ($this->getPreStartTime())
		{
			$transitionTimes[] = $startScreenTime - $this->getPreStartTime(); // start of preStart
		}
		$transitionTimes[] = $startScreenTime; // start of main content / end of preStart
		$transitionTimes[] = $startScreenTime + $this->getDuration(); // end of main content / start of postEnd
		if ($this->getPostEndTime())
		{
			$transitionTimes[] = $startScreenTime + $this->getDuration() + $this->getPostEndTime(); // end of postEnd
		}
		return $transitionTimes;
	}
}
