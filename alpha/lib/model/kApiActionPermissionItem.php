<?php
class kApiActionPermissionItem extends PermissionItem
{
	
	// column names
	
	const SERVICE_COLUMN_NAME = 'permission_item.PARAM_1';

	const ACTION_COLUMN_NAME = 'permission_item.PARAM_2';
	
	// public functions
	
	public function getService()
	{
		return $this->getParam1();
	}
	
	public function setService($service)
	{
		$this->setParam1($service);
	}
	
	public function getAction()
	{
		return $this->getParam2();
	}
	
	public function setAction($action)
	{
		$this->setParam2($action);
	}
		
}