<?php


class ComcastDayOfWeek extends SoapObject
{				
	const _SUNDAY = 'Sunday';
					
	const _MONDAY = 'Monday';
					
	const _TUESDAY = 'Tuesday';
					
	const _WEDNESDAY = 'Wednesday';
					
	const _THURSDAY = 'Thursday';
					
	const _FRIDAY = 'Friday';
					
	const _SATURDAY = 'Saturday';
					
	public function getType()
	{
		return 'urn:theplatform-com:v4/enum/:DayOfWeek';
	}
					
	protected function getAttributeType($attributeName)
	{
		switch($attributeName)
		{	
			default:
				return parent::getAttributeType($attributeName);
		}
	}
					
	public function __toString()
	{
		return print_r($this, true);	
	}
				
}


