<?php
class kApiParameterPermissionItem extends PermissionItem
{
	
	public function getObject()
	{
		return $this->getFromValue('object');
	}
	
	public function setObject($object)
	{
		$this->setInValue('object', $object);
	}
	
	public function getParameter()
	{
		return $this->getFromValue('parameter');
	}
	
	public function setParameter($parameter)
	{
		$this->setInValue('parameter', $parameter);
	}
	
	public function getAction()
	{
		return $this->getFromValue('action');
	}
	
	public function setAction($action)
	{
		$this->setInValue('action', $action);
	}
		
}