<?php
class kApiParameterPermissionItem extends PermissionItem
{
	
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