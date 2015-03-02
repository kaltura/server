<?php

require_once(__DIR__ . '/../ActivitiClient.php');
require_once(__DIR__ . '/../ActivitiService.php');
require_once(__DIR__ . '/../objects/ActivitiGetAnExecutionResponse.php');
require_once(__DIR__ . '/../objects/ActivitiExecuteAnActionOnAnExecutionResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetActiveActivitiesInAnExecutionResponse.php');
require_once(__DIR__ . '/../objects/ActivitiListOfExecutionsResponse.php');
require_once(__DIR__ . '/../objects/ActivitiQueryExecutionsResponse.php');
require_once(__DIR__ . '/../objects/ActivitiListOfVariablesForAnExecutionResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetVariableForAnExecutionResponse.php');
require_once(__DIR__ . '/../objects/ActivitiCreateorUpdateVariablesOnAnExecutionResponse.php');
require_once(__DIR__ . '/../objects/ActivitiUpdateVariableOnAnExecutionResponse.php');
require_once(__DIR__ . '/../objects/ActivitiCreateNewBinaryVariableOnAnExecutionResponse.php');
require_once(__DIR__ . '/../objects/ActivitiUpdateAnExistingBinaryVariableOnProcessinstanceResponse.php');
	

class ActivitiExecutionsService extends ActivitiService
{
	
	/**
	 * Get an execution
	 * 
	 * @return ActivitiGetAnExecutionResponse
	 * @see {@link http://www.activiti.org/userguide/#N14313 Get an execution}
	 */
	public function getAnExecution($executionId)
	{
		$data = array();
		
		return $this->client->request("runtime/executions/$executionId", 'GET', $data, array(200), array(404 => "Indicates the execution was not found."), 'ActivitiGetAnExecutionResponse');
	}
	
	/**
	 * Execute an action on an execution
	 * 
	 * @return ActivitiExecuteAnActionOnAnExecutionResponse
	 * @see {@link http://www.activiti.org/userguide/#N14359 Execute an action on an execution}
	 */
	public function executeAnActionOnAnExecution($executionId, $action = null, $signalName = null, $variables = null, $messageName = null)
	{
		$data = array();
		if(!is_null($action))
			$data['action'] = $action;
		if(!is_null($signalName))
			$data['signalName'] = $signalName;
		if(!is_null($variables))
			$data['variables'] = $variables;
		if(!is_null($messageName))
			$data['messageName'] = $messageName;
		
		return $this->client->request("runtime/executions/$executionId", 'PUT', $data, array(200,204), array(400 => "Indicates an illegal action was requested, required parameters are missing in the request body or illegal variables are passed in. Status description contains additional information about the error.",404 => "Indicates the execution was not found."), 'ActivitiExecuteAnActionOnAnExecutionResponse');
	}
	
	/**
	 * Get active activities in an execution
	 * 
	 * @return array<ActivitiGetActiveActivitiesInAnExecutionResponse>
	 * @see {@link http://www.activiti.org/userguide/#N143D0 Get active activities in an execution}
	 */
	public function getActiveActivitiesInAnExecution($executionId)
	{
		$data = array();
		
		return $this->client->request("runtime/executions/$executionId/activities", 'GET', $data, array(200), array(404 => "Indicates the execution was not found."), 'ActivitiGetActiveActivitiesInAnExecutionResponse', true);
	}
	
	/**
	 * List of executions
	 * 
	 * @return ActivitiListOfExecutionsResponse
	 * @see {@link http://www.activiti.org/userguide/#restExecutionsGet List of executions}
	 */
	public function listOfExecutions()
	{
		$data = array();
		
		return $this->client->request("runtime/executions", 'GET', $data, array(200), array(400 => "Indicates a parameter was passed in the wrong format . The status-message contains additional information."), 'ActivitiListOfExecutionsResponse');
	}
	
	/**
	 * Query executions
	 * 
	 * @return ActivitiQueryExecutionsResponse
	 * @see {@link http://www.activiti.org/userguide/#N144E1 Query executions}
	 */
	public function queryExecutions($processInstanceId = null, $variables = null, $processInstanceVariables = null)
	{
		$data = array();
		if(!is_null($processInstanceId))
			$data['processInstanceId'] = $processInstanceId;
		if(!is_null($variables))
			$data['variables'] = $variables;
		if(!is_null($processInstanceVariables))
			$data['processInstanceVariables'] = $processInstanceVariables;
		
		return $this->client->request("query/executions", 'POST', $data, array(200), array(400 => "Indicates a parameter was passed in the wrong format . The status-message contains additional information."), 'ActivitiQueryExecutionsResponse');
	}
	
	/**
	 * List of variables for an execution
	 * 
	 * @return array<ActivitiListOfVariablesForAnExecutionResponse>
	 * @see {@link http://www.activiti.org/userguide/#N14528 List of variables for an execution}
	 */
	public function listOfVariablesForAnExecution($executionId, $scope)
	{
		$data = array();
		
		return $this->client->request("runtime/executions/$executionId/variables?scope=$scope", 'GET', $data, array(200), array(404 => "Indicates the requested execution was not found."), 'ActivitiListOfVariablesForAnExecutionResponse', true);
	}
	
	/**
	 * Get a variable for an execution
	 * 
	 * @return ActivitiGetVariableForAnExecutionResponse
	 * @see {@link http://www.activiti.org/userguide/#N14580 Get a variable for an execution}
	 */
	public function getVariableForAnExecution($executionId, $variableName, $scope)
	{
		$data = array();
		
		return $this->client->request("runtime/executions/$executionId/variables/$variableName?scope=$scope", 'GET', $data, array(200), array(400 => "Indicates the request body is incomplete or contains illegal values. The status description contains additional information about the error.",404 => "Indicates the requested execution was not found or the execution does not have a variable with the given name in the requested scope (in case scope-query parameter was omitted, variable doesn't exist in local and global scope). Status description contains additional information about the error."), 'ActivitiGetVariableForAnExecutionResponse');
	}
	
	/**
	 * Create (or update) variables on an execution
	 * 
	 * @return array<ActivitiCreateorUpdateVariablesOnAnExecutionResponse>
	 * @see {@link http://www.activiti.org/userguide/#N145E6 Create (or update) variables on an execution}
	 */
	public function createorUpdateVariablesOnAnExecution($executionId, $data = null)
	{
		$data = array();
		if(!is_null($data))
			$data['data'] = $data;
		
		return $this->client->request("runtime/executions/$executionId/variables", 'POST', $data, array(201), array(400 => "Indicates the request body is incomplete or contains illegal values. The status description contains additional information about the error.",404 => "Indicates the requested execution was not found.",409 => "Indicates the execution was found but already contains a variable with the given name (only thrown when POST method is used). Use the update-method instead."), 'ActivitiCreateorUpdateVariablesOnAnExecutionResponse', true);
	}
	
	/**
	 * Update a variable on an execution
	 * 
	 * @return ActivitiUpdateVariableOnAnExecutionResponse
	 * @see {@link http://www.activiti.org/userguide/#N14653 Update a variable on an execution}
	 */
	public function updateVariableOnAnExecution($executionId, $variableName, $name = null, $type = null, $value = null, $scope = null)
	{
		$data = array();
		if(!is_null($name))
			$data['name'] = $name;
		if(!is_null($type))
			$data['type'] = $type;
		if(!is_null($value))
			$data['value'] = $value;
		if(!is_null($scope))
			$data['scope'] = $scope;
		
		return $this->client->request("runtime/executions/$executionId/variables/$variableName", 'PUT', $data, array(200), array(404 => "Indicates the requested process instance was not found or the process instance does not have a variable with the given name. Status description contains additional information about the error."), 'ActivitiUpdateVariableOnAnExecutionResponse');
	}
	
	/**
	 * Create a new binary variable on an execution
	 * 
	 * @return ActivitiCreateNewBinaryVariableOnAnExecutionResponse
	 * @see {@link http://www.activiti.org/userguide/#N146B2 Create a new binary variable on an execution}
	 */
	public function createNewBinaryVariableOnAnExecution($executionId)
	{
		$data = array();
		
		return $this->client->request("runtime/executions/$executionId/variables", 'POST', $data, array(201), array(400 => "Indicates the name of the variable to create was missing. Status message provides additional information.",404 => "Indicates the requested execution was not found.",409 => "Indicates the execution already has a variable with the given name. Use the PUT method to update the task variable instead.",415 => "Indicates the serializable data contains an object for which no class is present in the JVM running the Activiti engine and therefore cannot be deserialized."), 'ActivitiCreateNewBinaryVariableOnAnExecutionResponse');
	}
	
	/**
	 * Update an existing binary variable on a process-instance
	 * 
	 * @return ActivitiUpdateAnExistingBinaryVariableOnProcessinstanceResponse
	 * @see {@link http://www.activiti.org/userguide/#N14725 Update an existing binary variable on a process-instance}
	 */
	public function updateAnExistingBinaryVariableOnProcessinstance($executionId, $variableName)
	{
		$data = array();
		
		return $this->client->request("runtime/executions/$executionId/variables/$variableName", 'PUT', $data, array(200), array(400 => "Indicates the name of the variable to update was missing. Status message provides additional information.",404 => "Indicates the requested execution was not found or the execution does not have a variable with the given name.",415 => "Indicates the serializable data contains an object for which no class is present in the JVM running the Activiti engine and therefore cannot be deserialized."), 'ActivitiUpdateAnExistingBinaryVariableOnProcessinstanceResponse');
	}
	
}

