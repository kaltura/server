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


