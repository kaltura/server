<?php


class ComcastCustomFieldType extends SoapObject
{				
	const _BOOLEAN = 'Boolean';
					
	const _HTML = 'HTML';
					
	const _HYPERLINK = 'Hyperlink';
					
	const _IMAGE = 'Image';
					
	const _LARGE_TEXT = 'Large Text';
					
	const _TEXT = 'Text';
					
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


