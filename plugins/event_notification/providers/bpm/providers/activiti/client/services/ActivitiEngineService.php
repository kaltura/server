<?php

require_once(__DIR__ . '/../ActivitiClient.php');
require_once(__DIR__ . '/../ActivitiService.php');
require_once(__DIR__ . '/../objects/ActivitiGetEnginePropertiesResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetEngineInfoResponse.php');
	

class ActivitiEngineService extends ActivitiService
{
	
	/**
	 * Get engine properties
	 * 
	 * @return ActivitiGetEnginePropertiesResponse
	 * @see {@link http://www.activiti.org/userguide/#N15E25 Get engine properties}
	 */
	public function getEngineProperties()
	{
		$data = array();
		
		return $this->client->request("management/properties", 'GET', $data, array(200), array(), 'ActivitiGetEnginePropertiesResponse');
	}
	
	/**
	 * Get engine info
	 * 
	 * @return ActivitiGetEngineInfoResponse
	 * @see {@link http://www.activiti.org/userguide/#N15E4A Get engine info}
	 */
	public function getEngineInfo()
	{
		$data = array();
		
		return $this->client->request("management/engine", 'GET', $data, array(200), array(), 'ActivitiGetEngineInfoResponse');
	}
	
}

