<?php
class KontikiStorageProfile extends StorageProfile
{
    /**
     * @return string
     */
	public function getApiEntryPoint ()
	{
		return $this->getFromCustomData('kontiki_api_entry_point');
	}
	
	public function setApiEntryPoint ($v)
	{
		$this->putInCustomData('kontiki_api_entry_point', $v);
	}
	
    /**
     * @return string
     */
	public function getServiceToken ()
	{
		return $this->getFromCustomData('kontiki_service_token');
	}
	
	public function setServiceToken ($v)
	{
		$this->putInCustomData('kontiki_service_token', $v);
	}
	
    /**
     * @return string
     */
	public function getUserName ()
	{
		return $this->getFromCustomData('kontiki_user_name');
	}
	
	public function setUserName ($v)
	{
		$this->putInCustomData('kontiki_user_name', $v);
	}
	
    /**
     * @return string
     */
	public function getPassword ()
	{
		return $this->getFromCustomData('kontiki_password');
	}
	
	public function setPassword ($v)
	{
		$this->putInCustomData('kontiki_password', $v);
	}
}
