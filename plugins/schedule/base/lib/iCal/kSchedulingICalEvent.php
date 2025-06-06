<?php

class kSchedulingICalEvent extends kSchedulingICalComponent
{
	const SEC_IN_WEEK = 604800;
	const SEC_IN_MONTH = 2678400;
	const SEC_IN_YEAR = 31556926;
	const SEC_IN_DAY = 86400;
	const SEC_IN_HOUR = 3600;
	const SEC_IN_MINUTE = 60;
	const DATE_FORMAT = "Y-m-d\TH:i:sP";
	const UNIX_EPOCH_START_TIME_COMPACT = '19700101T000000';
	const UNIX_EPOCH_START_TIME = '1970-01-01T00:00:00+00:00';

	/**
	 * @var kSchedulingICalRule
	 */
	private $rule = null;

	private static $stringFields = array(
		'summary',
		'description',
		'status',
		'geoLatitude',
		'geoLongitude',
		'location',
		'priority',
		'sequence',
		'duration',
		'contact',
		'comment',
		'organizer',
	);

	private static $dateFields = array(
		'startDate' => 'dtstart',
		'endDate' => 'dtend',
	);

	protected static $timeZoneField = 'tzid';
	protected $timeZoneId = '';

	protected static function formatDurationString($durationStringInSeconds)
	{
		$duration = 'PT';
		$seconds = (int)$durationStringInSeconds;
		$hours = (int)($seconds / 3600);
		$minutes = (int)(($seconds - $hours * 3600) / 60);
		$secondsInt = (int)($seconds - $hours * 3600 - $minutes * 60);

		$duration = $duration . $hours . 'H';
		$duration = $duration . $minutes . 'M';
		$duration = $duration . $secondsInt . 'S';

		return $duration;
	}

	/**
	 * {@inheritDoc}
	 * @see kSchedulingICalComponent::getType()
	 */
	protected function getType()
	{
		return kSchedulingICal::TYPE_EVENT;
	}

	protected static function getUidFromEventId($id)
	{
		$hash = sha1($id);

		// Format the hash as a UUID
		$uuid = sprintf(
			'%08s-%04s-%04x-%04x-%12s',
			// 32 bits for "time_low"
			substr($hash, 0, 8),
			// 16 bits for "time_mid"
			substr($hash, 8, 4),
			// 16 bits for "time_hi_and_version", with version 4 UUID
			(hexdec(substr($hash, 12, 4)) & 0x0fff) | 0x4000,
			// 16 bits for "clk_seq_hi_res", with variant UUID
			(hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
			// 48 bits for "node"
			substr($hash, 20, 12)
		);

		return $uuid;
	}

	public function getUid()
	{
		return $this->getField('uid');
	}

	public function getMethod()
	{
		return $this->getField('method');
	}

	public function setRRule($rrule)
	{
		$this->rule = new kSchedulingICalRule($rrule);
	}

	/**
	 * @return kSchedulingICalRule
	 */
	public function getRule()
	{
		return $this->rule;
	}

	public function setRule(kSchedulingICalRule $rule)
	{
		$this->rule = $rule;
	}

	/**
	 * {@inheritDoc}
	 * @see kSchedulingICalComponent::writeBody()
	 */
	protected function writeBody()
	{
		$ret = parent::writeBody();

		if ($this->rule)
		{
			$ret .= $this->writeField('RRULE', $this->rule->getBody());
		}

		return $ret;
	}

	/**
	 * {@inheritDoc}
	 * @see kSchedulingICalComponent::toObject()
	 */
	public function toObject()
	{
		$type = $this->getKalturaType();
		$event = null;
		switch ($type)
		{
			case KalturaScheduleEventType::RECORD:
				$event = new KalturaRecordScheduleEvent();
				break;

			case KalturaScheduleEventType::LIVE_STREAM:
				$event = new KalturaLiveStreamScheduleEvent();
				break;

			case KalturaScheduleEventType::BLACKOUT:
				$event = new KalturaBlackoutScheduleEvent();
				break;

			default:
				throw new Exception("Event type [$type] not supported");
		}

		$event->referenceId = $this->getUid();

		foreach (self::$stringFields as $string)
		{
			$event->$string = $this->getField($string);
			if ( $string == 'duration')
			{
				$event->$string = $this->formatDuration($event->$string);
			}
		}

		foreach (self::$dateFields as $date => $field)
		{
			$configurationField = $this->getConfigurationField($field);
			$timezoneFormat = null;
			if ($configurationField != null)
			{
				if (preg_match('/"([^"]+)"/', $configurationField, $matches))
				{
					if (isset($matches[1]))
					{
						$timezoneFormat = $matches[1];
					}

				}
				elseif (preg_match('/=([^"]+)/', $configurationField, $matches))
				{
					if (isset($matches[1]))
					{
						$timezoneFormat = $matches[1];
					}
				}
			}
			$val = kSchedulingICal::parseDate($this->getField($field), $timezoneFormat);
			$event->$date = $val;
		}

		$classificationTypes = array(
			'PUBLIC' => KalturaScheduleEventClassificationType::PUBLIC_EVENT,
			'PRIVATE' => KalturaScheduleEventClassificationType::PRIVATE_EVENT,
			'CONFIDENTIAL' => KalturaScheduleEventClassificationType::CONFIDENTIAL_EVENT
		);

		$classificationType = $this->getField('class');
		if (isset($classificationTypes[$classificationType]))
		{
			$event->classificationType = $classificationTypes[$classificationType];
		}

		$rule = $this->getRule();
		if ($rule)
		{
			$event->recurrenceType = KalturaScheduleEventRecurrenceType::RECURRING;
			$event->recurrence = $rule->toObject();
		}
		else
		{
			$event->recurrenceType = KalturaScheduleEventRecurrenceType::NONE;
		}

		$event->parentId = $this->getField('x-kaltura-parent-id');
		$event->tags = $this->getField('x-kaltura-tags');
		$event->ownerId = $this->getField('x-kaltura-owner-id');

		if ($event instanceof KalturaEntryScheduleEvent)
		{
			$event->templateEntryId = $this->getField('x-kaltura-template-entry-id');
			$event->entryIds = $this->getField('x-kaltura-entry-ids');
			$event->categoryIds = $this->getField('x-kaltura-category-ids');
		}

		return $event;
	}

	/**
	 * @param KalturaScheduleEvent $event
	 * @return kSchedulingICalEvent
	 */
	public static function fromObject(KalturaScheduleEvent $event)
	{
		$newIcalFormat = !PermissionPeer::isValidForPartner(PermissionName::FEATURE_DISABLE_NEW_ICAL_STANDARD, $event->partnerId);
		$object = new kSchedulingICalEvent();
		$resourceIds = array();

		if ($event->referenceId)
		{
			$object->setField('uid', $event->referenceId);
		}
		elseif ($newIcalFormat)
		{
	        $object->setField('uid', self::getUidFromEventId($event->id));
		}

		if ($event->recurrence && $event->recurrence->timeZone)
		{
			$timeZones = DateTimeZone::listIdentifiers(DateTimeZone::ALL_WITH_BC);

			if (in_array($event->recurrence->timeZone, $timeZones))
			{
				$object->timeZoneId = $event->recurrence->timeZone;
			}
		}

		$resources = ScheduleEventResourcePeer::retrieveByEventIdOrItsParentId($event->id);
		foreach ($resources as $resource)
		{
			/* @var $resource ScheduleEventResource */
			$resourceIds[] = $resource->getResourceId();
		}
		$resourceIds = array_diff($resourceIds, array(0)); //resource 0 should not be exported outside of kaltura BE.

		foreach (self::$stringFields as $string)
		{
			if ($event->$string)
			{
				if ($string == 'duration')
				{
					if ($newIcalFormat && !is_null($event->endDate))
					{
						continue;
					}
					$duration = self::formatDurationString($event->$string);
					$object->setField($string, $duration);
				}
				elseif (($string == 'status') && ($newIcalFormat))
				{
					if ($event->$string == ScheduleEventStatus::ACTIVE)
					{
						$object->setField($string, 'CONFIRMED');
					}
					else
					{
						$object->setField($string, 'CANCELLED');
					}
				}
				else
				{
					$object->setField($string, $event->$string);
				}
			}
			elseif (($string == 'location') && $newIcalFormat)
			{
				if ($resourceIds)
				{
					$resourcesNames = array();
					$c = new Criteria();
					$c->add(ScheduleResourcePeer::PARTNER_ID, $event->partnerId);
					$c->add(ScheduleResourcePeer::ID, $resourceIds, Criteria::IN);
					$resources = ScheduleResourcePeer::doSelect($c);

					foreach ($resources as $resource)
					{
						/* @var $resource ScheduleResource */
						$resourcesNames[] = $resource->getName();
					}

					$object->setField($string, implode(',', $resourcesNames));
				}
			}
		}

		foreach (self::$dateFields as $date => $field)
		{
			if ($event->$date)
			{
				if (($object->timeZoneId !== '') && $newIcalFormat)
				{
					$fieldToUpperCase = $field . ";" . self::$timeZoneField . "=";
					$object->setField($fieldToUpperCase, kSchedulingICal::formatDate($event->$date, $object->timeZoneId), $object->timeZoneId);
				}
				else
				{
					$object->setField($field, kSchedulingICal::formatDate($event->$date));
				}
			}
		}

		$classificationTypes = array(
			KalturaScheduleEventClassificationType::PUBLIC_EVENT => 'PUBLIC',
			KalturaScheduleEventClassificationType::PRIVATE_EVENT => 'PRIVATE',
			KalturaScheduleEventClassificationType::CONFIDENTIAL_EVENT => 'CONFIDENTIAL'
		);

		if ($event->classificationType && isset($classificationTypes[$event->classificationType]))
		{
			$classificationType = $object->setField('class', $classificationTypes[$event->classificationType]);
		}

		if ($event->recurrence)
		{
			$rule = kSchedulingICalRule::fromObject($event->recurrence);
			$object->setRule($rule);
		}

		$object->setField('dtstamp', kSchedulingICal::formatDate($event->updatedAt));
		$object->setField('x-kaltura-id', $event->id);
		$object->setField('x-kaltura-type', $event->getScheduleEventType());
		$object->setField('x-kaltura-partner-id', $event->partnerId);
		$object->setField('x-kaltura-status', $event->status);
		$object->setField('x-kaltura-owner-id', $event->ownerId);

		if ($event->parentId)
		{
			$parent = ScheduleEventPeer::retrieveByPK($event->parentId);
			if ($parent)
			{
				$object->setField('x-kaltura-parent-id', $event->parentId);
				if ($parent->getReferenceId())
					$object->setField('x-kaltura-parent-uid', $parent->getReferenceId());

				if (!count($resourceIds))
				{
					$resources = ScheduleEventResourcePeer::retrieveByEventId($event->parentId);
					foreach ($resources as $resource)
					{
						/* @var $resource ScheduleEventResource */
						$resourceIds[] = $resource->getResourceId();
					}
				}
			}
		}

		if (count($resourceIds))
		{
			$object->setField('x-kaltura-resource-ids', implode(',', $resourceIds));
		}

		if ($event->tags)
		{
			$object->setField('x-kaltura-tags', $event->tags);
		}

		if ($event instanceof KalturaEntryScheduleEvent)
		{
			if ($event->templateEntryId)
			{
				$object->setField('x-kaltura-template-entry-id', $event->templateEntryId);
			}

			if ($event->entryIds)
			{
				$object->setField('x-kaltura-entry-ids', $event->entryIds);
			}

			if ($event->categoryIds)
			{
				$object->setField('x-kaltura-category-ids', $event->categoryIds);

				// hack, to be removed after x-kaltura-category-ids will be fully supported by other partners
				$pks = explode(',', $event->categoryIds);
				$categories = categoryPeer::retrieveByPKs($pks);
				$fullIds = array();
				foreach ($categories as $category)
				{
					/* @var $category category */
					$fullIds[] = $category->getFullIds();
				}
				if (count($fullIds))
				{
					$object->setField('related-to', implode(';', $fullIds));
				}
			}
		}

		if ($event->templateEntryId)
		{
			$entry = entryPeer::retrieveByPK($event->templateEntryId);
			if ( $entry && $entry->getType() == entryType::LIVE_STREAM)
			{
				/* @var $event LiveStreamEntry */
				$object->setField('x-kaltura-primary-rtmp-endpoint', $entry->getPrimaryBroadcastingUrl());
				$object->setField('x-kaltura-secondary-rtmp-endpoint', $entry->getSecondaryBroadcastingUrl());
				$object->setField('x-kaltura-primary-rtsp-endpoint', $entry->getPrimaryRtspBroadcastingUrl());
				$object->setField('x-kaltura-secondary-rtsp-endpoint', $entry->getSecondaryRtspBroadcastingUrl());
				$object->setField('x-kaltura-live-stream-name', $entry->getStreamName());
				$object->setField('x-kaltura-live-stream-username', $entry->getStreamUsername());
				$object->setField('x-kaltura-live-stream-password', $entry->getStreamPassword());
			}

		}

		return $object;
	}

	/**
	 * @param $duration
	 */
	private function formatDuration($duration)
	{
		if ($duration && ( $duration != '0' && intval($duration) == 0))
		{
			$datetime = new DateTime('@0');
			$datetime->add(new DateInterval($duration));
			$duration = $datetime->format('U');
		}
		return $duration;
	}


	public function getTimeZoneId()
	{
		return $this->timeZoneId;
	}

	protected function isTransitionsSyntetic($transitions, $startDate)
	{
		if (count($transitions) == 1)
		{
			$transitionTime = substr($transitions[0]['time'], 0, 10);
			$formattedStartDate = substr(dateUtils::kDate(self::DATE_FORMAT, $startDate), 0, 10);
			if ($transitionTime === $formattedStartDate)
			{
				return true;
			}
		}
		return false;
	}

	protected function refineTransitions(&$transitions, DateTimeZone $dateTimeZone, KalturaScheduleEvent $event, $startDate)
	{
		/**
		 * The blow is a use case were the country of the time zone does not observes daylight savings
		 * or might not contain transitions
		 */
		if (!$transitions || $this->isTransitionsSyntetic($transitions, $startDate))
		{
			$newStartDate = new DateTime('@' . $event->startDate, $dateTimeZone);
			$offsetInSeconds = $dateTimeZone->getOffset($newStartDate);
			$standardTransition = array(
				'ts' => 0,
				'time' => self::UNIX_EPOCH_START_TIME,
				'offset' => $offsetInSeconds,
				'isdst' => null,
				'abbr' => $dateTimeZone->getName()
			);
			$transitions = array($standardTransition);
		}

		return $transitions;
	}

	public function addVtimeZoneBlock(KalturaScheduleEvent $event = null, &$timeZoneBlockArray = null)
	{
		try
		{
			$dateTimeZone = new DateTimeZone($this->timeZoneId);
		}
		catch (Exception $e)
		{
			KalturaLog::err('Error while processing the time zone: ' . $e->getMessage());
			throw new KalturaAPIException('Error while processing the time zone: ' . $e->getMessage());
		}

		// Calculating until. Frequency is mandatory and also until or count
		$until = (!$event->recurrence->until) ? $this->getUntilFromCount($event->recurrence->count, $event->recurrence->frequency, $event->startDate) : $event->recurrence->until;

		/** In order to reduce the size of the transitions to analyze, we start querying from a year before the start
		 * of the event until the last occurrence.
		 * In cases where there are no transitions, one syntetic transition will be returned with the date of the even
		 * one year prior - we will refine and replace this transition as needed in refineTransitions()
		 */
		$startDateYearPreviously = dateUtils::getDateOnPreviousYear($event->startDate);
		$transitions = $dateTimeZone->getTransitions($startDateYearPreviously, $until);
		$this->refineTransitions($transitions, $dateTimeZone, $event, $startDateYearPreviously);

		$relevantTransitions = array();
		$initialTransition = null;
		$daylightOffset = null;
		$standardOffset = null;

		// This loop filters the list of transitions to only the ones that are relevant to the recurring event,
		// from the transition right before the start to the last transition during the event
		foreach ($transitions as $transition)
		{
			// Saving the daylight and standard offsets
			if ($transition['isdst'])
			{
				$daylightOffset = $transition['offset'];
			}
			else
			{
				$standardOffset = $transition['offset'];
			}

			if ($transition['ts'] <= $event->startDate)
			{
				$initialTransition = $transition;
			}
			if ($event->startDate <= $transition['ts'] && $transition['ts'] <= $until)
			{
				$relevantTransitions[] = $transition;
			}
		}
		array_unshift($relevantTransitions, $initialTransition);
		
		// Create VTIMEZONE block content
		$timeZoneBlockArray[] = $this->writeField(strtoupper(self::$timeZoneField), $this->timeZoneId);

		// Create internal Standard/Daylight blocks
		for ($i = 0; $i < count($relevantTransitions); $i++)
		{
			$timeZoneBlockArray[] = $this->buildTimeBlock($relevantTransitions[$i], $daylightOffset, $standardOffset);
		}
	}

	protected function getUntilFromCount($count, $frequency, $startDate)
	{
		switch ($frequency)
		{
			case DatesGenerator::SECONDLY:
			{
				return $startDate + $count;
			}
			case DatesGenerator::DAILY:
			{
				return $startDate + ($count * self::SEC_IN_DAY);
			}
			case DatesGenerator::MINUTELY:
			{
				return $startDate + ($count * self::SEC_IN_MINUTE);
			}
			case DatesGenerator::WEEKLY:
			{
				return $startDate + ($count * self::SEC_IN_WEEK);
			}
			case DatesGenerator::HOURLY:
			{
				return $startDate + ($count * self::SEC_IN_HOUR);
			}
			case DatesGenerator::MONTHLY:
			{
				return $startDate + ($count * self::SEC_IN_MONTH);
			}
			case DatesGenerator::YEARLY:
			{
				return $startDate + ($count * self::SEC_IN_YEAR);
			}
			default:
				return $startDate;
		}
	}

	protected function buildTimeBlock($transition, $daylightOffset, $standardOffset)
	{
		$transitionTimeBlock = '';
		$timeType = ($transition['isdst']) ? 'DAYLIGHT' : 'STANDARD';

		if (!$daylightOffset)
		{
			$daylightOffset = $standardOffset;
		}
		if (!$standardOffset)
		{
			$standardOffset = $daylightOffset;
		}

		$offsetFrom = ($timeType === 'STANDARD') ? dateUtils::formatOffset($daylightOffset) : dateUtils::formatOffset($standardOffset);
		$offsetTo = ($timeType === 'STANDARD') ? dateUtils::formatOffset($standardOffset) : dateUtils::formatOffset($daylightOffset);

		$transitionTimeBlock .= $this->writeField('BEGIN',$timeType);
		$transitionTimeBlock .= $this->writeField('TZOFFSETFROM', $offsetFrom);
		$transitionTimeBlock .= $this->writeField('TZOFFSETTO', $offsetTo);
		$transitionTimeBlock .= $this->writeField('TZNAME', $transition['abbr']);
		$dtstart = kSchedulingICal::formatTransitionDate($transition['ts']);
		$transitionTimeBlock .= $this->writeField('DTSTART', $dtstart);
		if ($dtstart != self::UNIX_EPOCH_START_TIME_COMPACT) // This date denotes timestamp 0. No reason to have a rule if the timezone does not observe daylight savings
		{
			$transitionTimeBlock .= $this->writeField('RRULE', "FREQ=YEARLY;BYMONTH=" . date('n', $transition['ts']) . ";BYDAY=" . dateUtils::convertWeekDay($transition['ts']));
		}
		$transitionTimeBlock .= $this->writeField('END', $timeType);

		return $transitionTimeBlock;
	}
}
