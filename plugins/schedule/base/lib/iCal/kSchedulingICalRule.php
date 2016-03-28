<?php

class kSchedulingICalRule extends kSchedulingICalComponent
{
	/**
	 * {@inheritDoc}
	 * @see kSchedulingICalComponent::getLineDelimiter()
	 */
	protected function getLineDelimiter()
	{
		return ";";
	}
	
	/**
	 * {@inheritDoc}
	 * @see kSchedulingICalComponent::getFieldDelimiter()
	 */
	protected function getFieldDelimiter()
	{
		return '=';
	}
	
	/**
	 * {@inheritDoc}
	 * @see kSchedulingICalComponent::getType()
	 */
	protected function getType()
	{
		return 'RRULE';
	}

	/**
	 * {@inheritDoc}
	 * @see kSchedulingICalComponent::toObject()
	 */
	public function toObject()
	{
		$rule = new KalturaScheduleEventRecurrence();
		$rule->frequency = constant('KalturaScheduleEventRecurrenceFrequency::' . $this->getField('freq'));
		$rule->until = kSchedulingICal::parseDate($this->getField('until'));

		$strings = array(
			'name',
			'count',
			'interval',
			'bySecond',
			'byMinute',
			'byHour',
			'byDay',
			'byMonthDay',
			'byYearDay',
			'byWeekNumber' => 'byweekno',
			'byMonth',
			'byOffset' => 'bysetpos',
			'weekStartDay' => 'wkst',
		);
		foreach($strings as $attribute => $field)
		{
			if(is_numeric($attribute))
				$attribute = $field;
			
			$rule->$attribute = $this->getField($field);
		}
		
		return $rule;
	}
}
