<?php
class kApiParameterPermissionItem extends PermissionItem
{
	// column names
	
	const OBJECT_COLUMN_NAME = 'permission_item.PARAM_1';

	const PARAMETER_COLUMN_NAME = 'permission_item.PARAM_2';
	
	const ACTION_COLUMN_NAME = 'permission_item.PARAM_3';
	
	
	// public functions
	
	public function getObject()
	{
		return $this->getParam1();
	}
	
	public function setObject($object)
	{
		$this->setParam1($object);
	}
	
	public function getParameter()
	{
		return $this->getParam2();
	}
	
	public function setParameter($parameter)
	{
		$this->setParam2($parameter);
	}
	
	public function getAction()
	{
		return $this->getParam3();
	}
	
	public function setAction($action)
	{
		$this->setParam3($action);
	}
		
}