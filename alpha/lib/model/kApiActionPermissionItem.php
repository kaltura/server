<?php
class kApiActionPermissionItem extends PermissionItem
{
	
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