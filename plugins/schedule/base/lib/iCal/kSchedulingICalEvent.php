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
		parent::writeBody();
		
		if($this->rule)
			$this->writeField('RRULE', $this->rule->getBody());
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
		$event->organizerUserId = $this->getField('organizer');

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
			$event->recurrences = array($rule->toObject());
		}
		else
		{
			$event->recurrenceType = KalturaScheduleEventRecurrenceType::NONE;
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

		if($event->organizerUserId)
			$object->setField('organizer', $event->organizerUserId);
		
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
			
		if($event->recurrences && count($event->recurrences))
		{
			$recurrence = reset($event->recurrences);
			/* @var $recurrence KalturaScheduleEventRecurrence */
			$rule = kSchedulingICalRule::fromObject($recurrence);
			$object->setRule($rule);
		}
		
		return $object;
	}
}
