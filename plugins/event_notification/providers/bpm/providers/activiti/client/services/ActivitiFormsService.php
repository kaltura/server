<?php

require_once(__DIR__ . '/../ActivitiClient.php');
require_once(__DIR__ . '/../ActivitiService.php');
require_once(__DIR__ . '/../objects/ActivitiGetFormDataResponse.php');
require_once(__DIR__ . '/../objects/ActivitiSubmitTaskFormDataResponse.php');
	

class ActivitiFormsService extends ActivitiService
{
	
	/**
	 * Get form data
	 * 
	 * @return ActivitiGetFormDataResponse
	 * @see {@link http://www.activiti.org/userguide/#N15C69 Get form data}
	 */
	public function getFormData()
	{
		$data = array();
		
		return $this->client->request("form/form-data", 'GET', $data, array(200), array(404 => "Indicates that form data could not be found."), 'ActivitiGetFormDataResponse');
	}
	
	/**
	 * Submit task form data
	 * 
	 * @return ActivitiSubmitTaskFormDataResponse
	 * @see {@link http://www.activiti.org/userguide/#N15CB8 Submit task form data}
	 */
	public function submitTaskFormData($taskId = null, $properties = null, $processDefinitionId = null, $businessKey = null)
	{
		$data = array();
		if(!is_null($taskId))
			$data['taskId'] = $taskId;
		if(!is_null($properties))
			$data['properties'] = $properties;
		if(!is_null($processDefinitionId))
			$data['processDefinitionId'] = $processDefinitionId;
		if(!is_null($businessKey))
			$data['businessKey'] = $businessKey;
		
		return $this->client->request("form/form-data", 'POST', $data, array(200), array(400 => "Indicates an parameter was passed in the wrong format. The status-message contains additional information."), 'ActivitiSubmitTaskFormDataResponse');
	}
	
}

