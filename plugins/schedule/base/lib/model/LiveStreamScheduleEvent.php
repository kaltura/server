<?php
/**
 * @package plugins.schedule
 * @subpackage model
 */
class LiveStreamScheduleEvent extends BaseLiveStreamScheduleEvent
{
	const PROJECTED_AUDIENCE = 'projected_audience';
	const PRE_START_TIME = 'pre_start_time';
	const POST_END_TIME = 'post_end_time';
	const SCREENING_START_TIME = 'screening_start_time';
	const SCREENING_END_TIME = 'screening_end_time';
	const SOURCE_ENTRY_ID = 'source_entry_id';
	
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
	 * @return int
	 */
	public function getPostEndTime()
	{
		return $this->getFromCustomData(self::POST_END_TIME, null, 0);
	}
	// In the old workflow, we did not save on the db the absolut start/end times.
	// we had the actual start/end times and the "paddings"
	// in the new workflow, start\end are mapped to the absolut and the actual play dates are
	// saved in custom data.
	// the bellow functions do the above mappings depending on the workflow for backwards compatibility
	// Objects created in the old workflow will not have 'screenEndTime' in custom data, therefore will return null
	protected function isOldWorkflow()
	{
		return is_null($this->getFromCustomData(self::SCREENING_END_TIME));
	}
	
	public function getEndScreenTime()
	{
		//For backwards compatibility
		if ($this->isOldWorkflow())
		{
			return $this->getEndDate();
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
		if ($this->isOldWorkflow())
		{
			return $this->getStartDate();
		}
		return $this->getFromCustomData(self::SCREENING_START_TIME);
	}
	
	public function getCalculatedStartTime()
	{
		//For backwards compatibility
		if ($this->isOldWorkflow())
		{
			return $this->getStartDate() - $this->getPreStartTime();
		}
		return parent::getCalculatedStartTime();
	}
	
	public function getCalculatedEndTime()
	{
		//For backwards compatibility
		if ($this->isOldWorkflow())
		{
			return $this->getEndDate() + $this->getPostEndTime();
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
				if($this->getSourceEntryId())
				{
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
}