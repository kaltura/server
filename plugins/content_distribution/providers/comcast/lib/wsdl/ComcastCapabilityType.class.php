<?php


class ComcastCapabilityType extends SoapObject
{				
	const _FULLCONTROL = 'FullControl';
					
	const _VIEW = 'View';
					
	const _ADD = 'Add';
					
	const _EDIT = 'Edit';
					
	const _DELETE = 'Delete';
					
	const _APPROVE = 'Approve';
					
	const _VIEWSELF = 'ViewSelf';
					
	const _EDITSELF = 'EditSelf';
					
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


