<?php

class kSchedulingICalEvent extends kSchedulingICalComponent
{
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
	
	/**
	 * {@inheritDoc}
	 * @see kSchedulingICalComponent::getType()
	 */
	protected function getType()
	{
		return kSchedulingICal::TYPE_EVENT;
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
		
		if($this->rule)
			$ret .= $this->writeField('RRULE', $this->rule->getBody());
		
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
		switch($type)
		{
			case KalturaScheduleEventType::RECORD:
				$event = new KalturaRecordScheduleEvent();
				break;

			case KalturaScheduleEventType::LIVE_STREAM:
				$event = new KalturaLiveStreamScheduleEvent();
				break;
				
			default:
				throw new Exception("Event type [$type] not supported");
		}

		$event->referenceId = $this->getUid();

		foreach(self::$stringFields as $string)
		{
			$event->$string = $this->getField($string);
		}

		foreach(self::$dateFields as $date => $field)
		{
			$event->$date = kSchedulingICal::parseDate($this->getField($field));
		}
		
		$classificationTypes = array(
			'PUBLIC' => KalturaScheduleEventClassificationType::PUBLIC_EVENT,
			'PRIVATE' => KalturaScheduleEventClassificationType::PRIVATE_EVENT,
			'CONFIDENTIAL' => KalturaScheduleEventClassificationType::CONFIDENTIAL_EVENT
		);
		
		$classificationType = $this->getField('class');
		if(isset($classificationTypes[$classificationType]))
			$event->classificationType = $classificationTypes[$classificationType];

		$rule = $this->getRule();
		if($rule)
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
		
		if($event instanceof KalturaEntryScheduleEvent)
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
		$object = new kSchedulingICalEvent();

		if($event->referenceId)
			$object->setField('uid', $event->referenceId);
		
		foreach(self::$stringFields as $string)
		{
			if($event->$string)
				$object->setField($string, $event->$string);
		}
		
		foreach(self::$dateFields as $date => $field)
		{
			if($event->$date)
				$object->setField($field, kSchedulingICal::formatDate($event->$date));
		}
		
		$classificationTypes = array(
			KalturaScheduleEventClassificationType::PUBLIC_EVENT => 'PUBLIC',
			KalturaScheduleEventClassificationType::PRIVATE_EVENT => 'PRIVATE',
			KalturaScheduleEventClassificationType::CONFIDENTIAL_EVENT => 'CONFIDENTIAL'
		);

		if($event->classificationType && isset($classificationTypes[$event->classificationType]))
			$classificationType = $object->setField('class', $classificationTypes[$event->classificationType]);
			
		if($event->recurrence)
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
		

		if($event->parentId)
		{
			$parent = ScheduleEventPeer::retrieveByPK($event->parentId);
			if($parent)
			{
				$object->setField('x-kaltura-parent-id', $event->parentId);
				if($parent->getReferenceId())
					$object->setField('x-kaltura-parent-uid', $parent->getReferenceId());
			}
		}

		if($event->tags)
			$object->setField('x-kaltura-tags', $event->tags);
		
		if($event instanceof KalturaEntryScheduleEvent)
		{
			if($event->templateEntryId)
				$object->setField('x-kaltura-template-entry-id', $event->templateEntryId);

			if($event->entryIds)
				$object->setField('x-kaltura-entry-ids', $event->entryIds);
				
			if($event->categoryIds)
			{
				$object->setField('x-kaltura-category-ids', $event->categoryIds);
				
				// hack, to be removed after x-kaltura-category-ids will be fully supported by other partners
				$pks = explode(',', $event->categoryIds);
				$categories = categoryPeer::retrieveByPKs($pks);
				$fullIds = array();
				foreach($categories as $category)
				{
					/* @var $category category */
					$fullIds[] = $category->getFullIds();
				}
				if(count($fullIds))
					$object->setField('related-to', implode(';', $fullIds));
			}
		}

		$resources = ScheduleEventResourcePeer::retrieveByEventId($event->id);
		$resourceIds = array();
		foreach($resources as $resource)
		{
			/* @var $resource ScheduleEventResource */
			$resourceIds[] = $resource->getResourceId();
		}
		if(count($resourceIds))
			$object->setField('x-kaltura-resource-ids', implode(',', $resourceIds));
		
		return $object;
	}
}
