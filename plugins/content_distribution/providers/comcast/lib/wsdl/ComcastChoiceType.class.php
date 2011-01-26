<?php


class ComcastChoiceType extends SoapObject
{				
	const _DYNAMIC = 'Dynamic';
					
	const _PLACEHOLDER = 'Placeholder';
					
	const _STATIC = 'Static';
					
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


