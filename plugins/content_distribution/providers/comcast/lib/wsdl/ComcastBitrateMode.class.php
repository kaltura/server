<?php


class ComcastBitrateMode extends SoapObject
{				
	const _CONSTANT = 'Constant';
					
	const _VARIABLEWITHMAXIMUM = 'VariableWithMaximum';
					
	const _VARIABLEWITHOUTMAXIMUM = 'VariableWithoutMaximum';
					
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


