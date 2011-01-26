<?php


class ComcastContentType extends SoapObject
{				
	const _ANIMATION = 'Animation';
					
	const _AUDIO = 'Audio';
					
	const _DOCUMENT = 'Document';
					
	const _EXECUTABLE = 'Executable';
					
	const _IMAGE = 'Image';
					
	const _MIXED = 'Mixed';
					
	const _UNKNOWN = 'Unknown';
					
	const _VIDEO = 'Video';
					
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


