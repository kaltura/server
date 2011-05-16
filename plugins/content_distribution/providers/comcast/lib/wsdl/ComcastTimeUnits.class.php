<?php


class ComcastTimeUnits extends SoapObject
{				
	const _MINUTES = 'minutes';
					
	const _HOURS = 'hours';
					
	const _DAYS = 'days';
					
	const _WEEKS = 'weeks';
					
	const _MONTHS = 'months';
					
	const _YEARS = 'years';
					
	public function getType()
	{
		return 'urn:theplatform-com:v4/base/:TimeUnits';
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


