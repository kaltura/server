<?php

class kSchedulingICalEvent extends kSchedulingICalComponent
{
	private $rules = array();
	
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
		$this->rules = array(new kSchedulingICalRule($rrule));
	}
	
	public function getRules()
	{
		return $this->rules;
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

		$strings = array(
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
		foreach($strings as $string)
		{
			$event->$string = $this->getField($string);
		}

		$dates = array(
			'startDate' => 'dtstart',
			'endDate' => 'dtend',
		);
		foreach($dates as $date => $field)
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

		$rules = $this->getRules();
		if($rules)
		{
			$event->recurrenceType = KalturaScheduleEventRecurrenceType::RECURRING;
			$event->recurrences = array();
			foreach($rules as $rule)
			{
				/* @var $rule kSchedulingICalRule */
				$event->recurrences[] = $rule->toObject();
			}
		}
		else
		{
			$event->recurrenceType = KalturaScheduleEventRecurrenceType::NONE;
		}
						
		return $event;
	}
}
