<?php
class kApiActionPermissionItem extends PermissionItem
{
	
	public function getService()
	{
		return $this->getFromValue('service');
	}
	
	public function setService($service)
	{
		$this->setInValue('service', $service);
	}
	
	public function getAction()
	{
		return $this->getFromValue('action');
	}
	
	public function setAction($action)
	{
		$this->setInValue('action', $action);
	}
	
	public function getPartnerGroup()
	{
		return $this->getFromValue('partner_group');
	}
	
	public function setPartnerGroup($group)
	{
		$this->setInValue('partner_group', $group);
	}		
}