<?php


class ComcastFieldState extends SoapObject
{				
	const _U = 'U';
					
	const _P = 'P';
					
	const _R = 'R';
					
	const _W = 'W';
					
	const _RW = 'RW';
					
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


