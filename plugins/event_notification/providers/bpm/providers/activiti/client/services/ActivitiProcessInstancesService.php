<?php

require_once(__DIR__ . '/../ActivitiClient.php');
require_once(__DIR__ . '/../ActivitiService.php');
require_once(__DIR__ . '/../objects/ActivitiGetProcessInstanceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiStartProcessInstanceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiListOfProcessInstancesResponse.php');
require_once(__DIR__ . '/../objects/ActivitiQueryProcessInstancesResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetDiagramForProcessInstanceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetInvolvedPeopleForProcessInstanceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiAddAnInvolvedUserToProcessInstanceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiRemoveAnInvolvedUserToFromProcessInstanceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiListOfVariablesForProcessInstanceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetVariableForProcessInstanceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiCreateorUpdateVariablesOnProcessInstanceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiUpdateSingleVariableOnProcessInstanceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiCreateNewBinaryVariableOnProcessinstanceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiUpdateAnExistingBinaryVariableOnProcessinstanceResponse.php');
	

class ActivitiProcessInstancesService extends ActivitiService
{
	
	/**
	 * Get a process instance
	 * 
	 * @return ActivitiGetProcessInstanceResponse
	 * @see {@link http://www.activiti.org/userguide/#N13D15 Get a process instance}
	 */
	public function getProcessInstance($processInstanceId)
	{
		$data = array();
		
		return $this->client->request("runtime/process-instances/$processInstanceId", 'GET', $data, array(200), array(404 => "Indicates the requested process instance was not found."), 'ActivitiGetProcessInstanceResponse');
	}
	
	/**
	 * Delete a process instance
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N13D5B Delete a process instance}
	 */
	public function deleteProcessInstance($processInstanceId)
	{
		$data = array();
		
		return $this->client->request("runtime/process-instances/$processInstanceId", 'DELETE', $data, array(204), array(404 => "Indicates the requested process instance was not found."));
	}
	
	/**
	 * Activate or suspend a process instance
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N13D98 Activate or suspend a process instance}
	 */
	public function activateOrSuspendProcessInstance($processInstanceId)
	{
		$data = array();
		
		return $this->client->request("runtime/process-instances/$processInstanceId", 'PUT', $data, array(200), array(400 => "Indicates an invalid action was supplied.",404 => "Indicates the requested process instance was not found.",409 => "Indicates the requested process instance action cannot be executed since the process-instance is already activated/suspended."));
	}
	
	/**
	 * Start a process instance
	 * 
	 * @return ActivitiStartProcessInstanceResponse
	 * @see {@link http://www.activiti.org/userguide/#N13DF1 Start a process instance}
	 */
	public function startProcessInstance($processDefinitionId = null, $businessKey = null, $variables = null, $processDefinitionKey = null, $tenantId = null, $message = null)
	{
		$data = array();
		if(!is_null($processDefinitionId))
			$data['processDefinitionId'] = $processDefinitionId;
		if(!is_null($businessKey))
			$data['businessKey'] = $businessKey;
		if(!is_null($variables))
			$data['variables'] = $variables;
		if(!is_null($processDefinitionKey))
			$data['processDefinitionKey'] = $processDefinitionKey;
		if(!is_null($tenantId))
			$data['tenantId'] = $tenantId;
		if(!is_null($message))
			$data['message'] = $message;
		
		return $this->client->request("runtime/process-instances", 'POST', $data, array(201), array(400 => "Indicates either the process-definition was not found (based on id or key), no process is started by sending the given message or an invalid variable has been passed. Status description contains additional information about the error."), 'ActivitiStartProcessInstanceResponse');
	}
	
	/**
	 * List of process instances
	 * 
	 * @return ActivitiListOfProcessInstancesResponse
	 * @see {@link http://www.activiti.org/userguide/#restProcessInstancesGet List of process instances}
	 */
	public function listOfProcessInstances()
	{
		$data = array();
		
		return $this->client->request("runtime/process-instances", 'GET', $data, array(200), array(400 => "Indicates a parameter was passed in the wrong format . The status-message contains additional information."), 'ActivitiListOfProcessInstancesResponse');
	}
	
	/**
	 * Query process instances
	 * 
	 * @return ActivitiQueryProcessInstancesResponse
	 * @see {@link http://www.activiti.org/userguide/#N13F35 Query process instances}
	 */
	public function queryProcessInstances($processDefinitionKey = null, $variables = null)
	{
		$data = array();
		if(!is_null($processDefinitionKey))
			$data['processDefinitionKey'] = $processDefinitionKey;
		if(!is_null($variables))
			$data['variables'] = $variables;
		
		return $this->client->request("query/process-instances", 'POST', $data, array(200), array(400 => "Indicates a parameter was passed in the wrong format . The status-message contains additional information."), 'ActivitiQueryProcessInstancesResponse');
	}
	
	/**
	 * Get diagram for a process instance
	 * 
	 * @return ActivitiGetDiagramForProcessInstanceResponse
	 * @see {@link http://www.activiti.org/userguide/#N13F76 Get diagram for a process instance}
	 */
	public function getDiagramForProcessInstance($processInstanceId)
	{
		$data = array();
		
		return $this->client->request("runtime/process-instances/$processInstanceId/diagram", 'GET', $data, array(200), array(400 => "Indicates the requested process instance was not found but the process doesn't contain any graphical information (BPMN:DI) and no diagram can be created.",404 => "Indicates the requested process instance was not found."), 'ActivitiGetDiagramForProcessInstanceResponse');
	}
	
	/**
	 * Get involved people for process instance
	 * 
	 * @return array<ActivitiGetInvolvedPeopleForProcessInstanceResponse>
	 * @see {@link http://www.activiti.org/userguide/#N13FC1 Get involved people for process instance}
	 */
	public function getInvolvedPeopleForProcessInstance($processInstanceId)
	{
		$data = array();
		
		return $this->client->request("runtime/process-instances/$processInstanceId/identitylinks", 'GET', $data, array(200), array(404 => "Indicates the requested process instance was not found."), 'ActivitiGetInvolvedPeopleForProcessInstanceResponse', true);
	}
	
	/**
	 * Add an involved user to a process instance
	 * 
	 * @return ActivitiAddAnInvolvedUserToProcessInstanceResponse
	 * @see {@link http://www.activiti.org/userguide/#N1400A Add an involved user to a process instance}
	 */
	public function addAnInvolvedUserToProcessInstance($processInstanceId, $userId = null, $type = null)
	{
		$data = array();
		if(!is_null($userId))
			$data['userId'] = $userId;
		if(!is_null($type))
			$data['type'] = $type;
		
		return $this->client->request("runtime/process-instances/$processInstanceId/identitylinks", 'POST', $data, array(201), array(400 => "Indicates the requested body did not contain a userId or a type.",404 => "Indicates the requested process instance was not found."), 'ActivitiAddAnInvolvedUserToProcessInstanceResponse');
	}
	
	/**
	 * Remove an involved user to from process instance
	 * 
	 * @return ActivitiRemoveAnInvolvedUserToFromProcessInstanceResponse
	 * @see {@link http://www.activiti.org/userguide/#N14067 Remove an involved user to from process instance}
	 */
	public function removeAnInvolvedUserToFromProcessInstance($processInstanceId, $userId, $type)
	{
		$data = array();
		
		return $this->client->request("runtime/process-instances/$processInstanceId/identitylinks/users/$userId/$type", 'DELETE', $data, array(204), array(404 => "Indicates the requested process instance was not found or the link to delete doesn't exist. The response status contains additional information about the error."), 'ActivitiRemoveAnInvolvedUserToFromProcessInstanceResponse');
	}
	
	/**
	 * List of variables for a process instance
	 * 
	 * @return array<ActivitiListOfVariablesForProcessInstanceResponse>
	 * @see {@link http://www.activiti.org/userguide/#N140C2 List of variables for a process instance}
	 */
	public function listOfVariablesForProcessInstance($processInstanceId)
	{
		$data = array();
		
		return $this->client->request("runtime/process-instances/$processInstanceId/variables", 'GET', $data, array(200), array(404 => "Indicates the requested process instance was not found."), 'ActivitiListOfVariablesForProcessInstanceResponse', true);
	}
	
	/**
	 * Get a variable for a process instance
	 * 
	 * @return ActivitiGetVariableForProcessInstanceResponse
	 * @see {@link http://www.activiti.org/userguide/#N14111 Get a variable for a process instance}
	 */
	public function getVariableForProcessInstance($processInstanceId, $variableName)
	{
		$data = array();
		
		return $this->client->request("runtime/process-instances/$processInstanceId/variables/$variableName", 'GET', $data, array(200), array(400 => "Indicates the request body is incomplete or contains illegal values. The status description contains additional information about the error.",404 => "Indicates the requested process instance was not found or the process instance does not have a variable with the given name. Status description contains additional information about the error."), 'ActivitiGetVariableForProcessInstanceResponse');
	}
	
	/**
	 * Create (or update) variables on a process instance
	 * 
	 * @return array<ActivitiCreateorUpdateVariablesOnProcessInstanceResponse>
	 * @see {@link http://www.activiti.org/userguide/#N1416E Create (or update) variables on a process instance}
	 */
	public function createorUpdateVariablesOnProcessInstance($processInstanceId, $data = null)
	{
		$data = array();
		if(!is_null($data))
			$data['data'] = $data;
		
		return $this->client->request("runtime/process-instances/$processInstanceId/variables", 'POST', $data, array(201), array(400 => "Indicates the request body is incomplete or contains illegal values. The status description contains additional information about the error.",404 => "Indicates the requested process instance was not found.",409 => "Indicates the process instance was found but already contains a variable with the given name (only thrown when POST method is used). Use the update-method instead."), 'ActivitiCreateorUpdateVariablesOnProcessInstanceResponse', true);
	}
	
	/**
	 * Update a single variable on a process instance
	 * 
	 * @return ActivitiUpdateSingleVariableOnProcessInstanceResponse
	 * @see {@link http://www.activiti.org/userguide/#N141D7 Update a single variable on a process instance}
	 */
	public function updateSingleVariableOnProcessInstance($processInstanceId, $variableName, $name = null, $type = null, $value = null)
	{
		$data = array();
		if(!is_null($name))
			$data['name'] = $name;
		if(!is_null($type))
			$data['type'] = $type;
		if(!is_null($value))
			$data['value'] = $value;
		
		return $this->client->request("runtime/process-instances/$processInstanceId/variables/$variableName", 'PUT', $data, array(200), array(404 => "Indicates the requested process instance was not found or the process instance does not have a variable with the given name. Status description contains additional information about the error."), 'ActivitiUpdateSingleVariableOnProcessInstanceResponse');
	}
	
	/**
	 * Create a new binary variable on a process-instance
	 * 
	 * @return ActivitiCreateNewBinaryVariableOnProcessinstanceResponse
	 * @see {@link http://www.activiti.org/userguide/#N1423F Create a new binary variable on a process-instance}
	 */
	public function createNewBinaryVariableOnProcessinstance($processInstanceId)
	{
		$data = array();
		
		return $this->client->request("runtime/process-instances/$processInstanceId/variables", 'POST', $data, array(201), array(400 => "Indicates the name of the variable to create was missing. Status message provides additional information.",404 => "Indicates the requested process instance was not found.",409 => "Indicates the process instance already has a variable with the given name. Use the PUT method to update the task variable instead.",415 => "Indicates the serializable data contains an object for which no class is present in the JVM running the Activiti engine and therefore cannot be deserialized."), 'ActivitiCreateNewBinaryVariableOnProcessinstanceResponse');
	}
	
	/**
	 * Update an existing binary variable on a process-instance
	 * 
	 * @return ActivitiUpdateAnExistingBinaryVariableOnProcessinstanceResponse
	 * @see {@link http://www.activiti.org/userguide/#N142AA Update an existing binary variable on a process-instance}
	 */
	public function updateAnExistingBinaryVariableOnProcessinstance($processInstanceId)
	{
		$data = array();
		
		return $this->client->request("runtime/process-instances/$processInstanceId/variables", 'PUT', $data, array(200), array(400 => "Indicates the name of the variable to update was missing. Status message provides additional information.",404 => "Indicates the requested process instance was not found or the process instance does not have a variable with the given name.",415 => "Indicates the serializable data contains an object for which no class is present in the JVM running the Activiti engine and therefore cannot be deserialized."), 'ActivitiUpdateAnExistingBinaryVariableOnProcessinstanceResponse');
	}
	
}

