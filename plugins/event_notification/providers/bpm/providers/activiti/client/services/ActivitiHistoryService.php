<?php

require_once(__DIR__ . '/../ActivitiClient.php');
require_once(__DIR__ . '/../ActivitiService.php');
require_once(__DIR__ . '/../objects/ActivitiGetHistoricProcessInstanceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiListOfHistoricProcessInstancesResponse.php');
require_once(__DIR__ . '/../objects/ActivitiQueryForHistoricProcessInstancesResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetTheIdentityLinksOfHistoricProcessInstanceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiCreateNewCommentOnHistoricProcessInstanceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetAllCommentsOnHistoricProcessInstanceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetCommentOnHistoricProcessInstanceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetSingleHistoricTaskInstanceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetHistoricTaskInstancesResponse.php');
require_once(__DIR__ . '/../objects/ActivitiQueryForHistoricTaskInstancesResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetTheIdentityLinksOfHistoricTaskInstanceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetHistoricActivityInstancesResponse.php');
require_once(__DIR__ . '/../objects/ActivitiQueryForHistoricActivityInstancesResponse.php');
require_once(__DIR__ . '/../objects/ActivitiListOfHistoricVariableInstancesResponse.php');
require_once(__DIR__ . '/../objects/ActivitiQueryForHistoricVariableInstancesResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetHistoricDetailResponse.php');
require_once(__DIR__ . '/../objects/ActivitiQueryForHistoricDetailsResponse.php');
	

class ActivitiHistoryService extends ActivitiService
{
	
	/**
	 * Get a historic process instance
	 * 
	 * @return ActivitiGetHistoricProcessInstanceResponse
	 * @see {@link http://www.activiti.org/userguide/#N153DC Get a historic process instance}
	 */
	public function getHistoricProcessInstance($processInstanceId)
	{
		$data = array();
		
		return $this->client->request("history/historic-process-instances/$processInstanceId", 'GET', $data, array(200), array(404 => "Indicates that the historic process instances could not be found."), 'ActivitiGetHistoricProcessInstanceResponse');
	}
	
	/**
	 * List of historic process instances
	 * 
	 * @return ActivitiListOfHistoricProcessInstancesResponse
	 * @see {@link http://www.activiti.org/userguide/#restHistoricProcessInstancesGet List of historic process instances}
	 */
	public function listOfHistoricProcessInstances()
	{
		$data = array();
		
		return $this->client->request("history/historic-process-instances", 'GET', $data, array(200), array(400 => "Indicates an parameter was passed in the wrong format. The status-message contains additional information."), 'ActivitiListOfHistoricProcessInstancesResponse');
	}
	
	/**
	 * Query for historic process instances
	 * 
	 * @return ActivitiQueryForHistoricProcessInstancesResponse
	 * @see {@link http://www.activiti.org/userguide/#N154F0 Query for historic process instances}
	 */
	public function queryForHistoricProcessInstances($processDefinitionId = null, $variables = null)
	{
		$data = array();
		if(!is_null($processDefinitionId))
			$data['processDefinitionId'] = $processDefinitionId;
		if(!is_null($variables))
			$data['variables'] = $variables;
		
		return $this->client->request("query/historic-process-instances", 'POST', $data, array(200), array(400 => "Indicates an parameter was passed in the wrong format. The status-message contains additional information."), 'ActivitiQueryForHistoricProcessInstancesResponse');
	}
	
	/**
	 * Delete a historic process instance
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N15530 Delete a historic process instance}
	 */
	public function deleteHistoricProcessInstance($processInstanceId)
	{
		$data = array();
		
		return $this->client->request("history/historic-process-instances/$processInstanceId", 'DELETE', $data, array(200), array());
	}
	
	/**
	 * Get the identity links of a historic process instance
	 * 
	 * @return array<ActivitiGetTheIdentityLinksOfHistoricProcessInstanceResponse>
	 * @see {@link http://www.activiti.org/userguide/#N15551 Get the identity links of a historic process instance}
	 */
	public function getTheIdentityLinksOfHistoricProcessInstance($processInstanceId)
	{
		$data = array();
		
		return $this->client->request("history/historic-process-instance/$processInstanceId/identitylinks", 'GET', $data, array(200), array(), 'ActivitiGetTheIdentityLinksOfHistoricProcessInstanceResponse', true);
	}
	
	/**
	 * Get the binary data for a historic process instance variable
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N1557B Get the binary data for a historic process instance variable}
	 */
	public function getTheBinaryDataForHistoricProcessInstanceVariable($processInstanceId, $variableName)
	{
		$data = array();
		
		return $this->client->request("history/historic-process-instances/$processInstanceId/variables/$variableName/data", 'GET', $data, array(200), array(404 => "Indicates the requested process instance was not found or the process instance doesn't have a variable with the given name or the variable doesn't have a binary stream available. Status message provides additional information."));
	}
	
	/**
	 * Create a new comment on a historic process instance
	 * 
	 * @return ActivitiCreateNewCommentOnHistoricProcessInstanceResponse
	 * @see {@link http://www.activiti.org/userguide/#N155AE Create a new comment on a historic process instance}
	 */
	public function createNewCommentOnHistoricProcessInstance($processInstanceId, $message = null, $saveProcessInstanceId = null)
	{
		$data = array();
		if(!is_null($message))
			$data['message'] = $message;
		if(!is_null($saveProcessInstanceId))
			$data['saveProcessInstanceId'] = $saveProcessInstanceId;
		
		return $this->client->request("history/historic-process-instances/$processInstanceId/comments", 'POST', $data, array(201), array(400 => "Indicates the comment is missing from the request.",404 => "Indicates the requested historic process instance was not found."), 'ActivitiCreateNewCommentOnHistoricProcessInstanceResponse');
	}
	
	/**
	 * Get all comments on a historic process instance
	 * 
	 * @return array<ActivitiGetAllCommentsOnHistoricProcessInstanceResponse>
	 * @see {@link http://www.activiti.org/userguide/#N15608 Get all comments on a historic process instance}
	 */
	public function getAllCommentsOnHistoricProcessInstance($processInstanceId)
	{
		$data = array();
		
		return $this->client->request("history/historic-process-instances/$processInstanceId/comments", 'GET', $data, array(200), array(404 => "Indicates the requested task was not found."), 'ActivitiGetAllCommentsOnHistoricProcessInstanceResponse', true);
	}
	
	/**
	 * Get a comment on a historic process instance
	 * 
	 * @return ActivitiGetCommentOnHistoricProcessInstanceResponse
	 * @see {@link http://www.activiti.org/userguide/#N1564C Get a comment on a historic process instance}
	 */
	public function getCommentOnHistoricProcessInstance($processInstanceId, $commentId)
	{
		$data = array();
		
		return $this->client->request("history/historic-process-instances/$processInstanceId/comments/$commentId", 'GET', $data, array(200), array(404 => "Indicates the requested historic process instance was not found or the historic process instance doesn't have a comment with the given ID."), 'ActivitiGetCommentOnHistoricProcessInstanceResponse');
	}
	
	/**
	 * Delete a comment on a historic process instance
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N15699 Delete a comment on a historic process instance}
	 */
	public function deleteCommentOnHistoricProcessInstance($processInstanceId, $commentId)
	{
		$data = array();
		
		return $this->client->request("history/historic-process-instances/$processInstanceId/comments/$commentId", 'DELETE', $data, array(204), array(404 => "Indicates the requested task was not found or the historic process instance doesn't have a comment with the given ID."));
	}
	
	/**
	 * Get a single historic task instance
	 * 
	 * @return ActivitiGetSingleHistoricTaskInstanceResponse
	 * @see {@link http://www.activiti.org/userguide/#N156DC Get a single historic task instance}
	 */
	public function getSingleHistoricTaskInstance($taskId)
	{
		$data = array();
		
		return $this->client->request("history/historic-task-instances/$taskId", 'GET', $data, array(200), array(404 => "Indicates that the historic task instances could not be found."), 'ActivitiGetSingleHistoricTaskInstanceResponse');
	}
	
	/**
	 * Get historic task instances
	 * 
	 * @return ActivitiGetHistoricTaskInstancesResponse
	 * @see {@link http://www.activiti.org/userguide/#restHistoricTaskInstancesGet Get historic task instances}
	 */
	public function getHistoricTaskInstances()
	{
		$data = array();
		
		return $this->client->request("history/historic-task-instances", 'GET', $data, array(200), array(400 => "Indicates an parameter was passed in the wrong format. The status-message contains additional information."), 'ActivitiGetHistoricTaskInstancesResponse');
	}
	
	/**
	 * Query for historic task instances
	 * 
	 * @return ActivitiQueryForHistoricTaskInstancesResponse
	 * @see {@link http://www.activiti.org/userguide/#N158D4 Query for historic task instances}
	 */
	public function queryForHistoricTaskInstances()
	{
		$data = array();
		
		return $this->client->request("query/historic-task-instances", 'POST', $data, array(200), array(400 => "Indicates an parameter was passed in the wrong format. The status-message contains additional information."), 'ActivitiQueryForHistoricTaskInstancesResponse');
	}
	
	/**
	 * Delete a historic task instance
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N15917 Delete a historic task instance}
	 */
	public function deleteHistoricTaskInstance($taskId)
	{
		$data = array();
		
		return $this->client->request("history/historic-task-instances/$taskId", 'DELETE', $data, array(200), array());
	}
	
	/**
	 * Get the identity links of a historic task instance
	 * 
	 * @return array<ActivitiGetTheIdentityLinksOfHistoricTaskInstanceResponse>
	 * @see {@link http://www.activiti.org/userguide/#N15938 Get the identity links of a historic task instance}
	 */
	public function getTheIdentityLinksOfHistoricTaskInstance($taskId)
	{
		$data = array();
		
		return $this->client->request("history/historic-task-instance/$taskId/identitylinks", 'GET', $data, array(200), array(), 'ActivitiGetTheIdentityLinksOfHistoricTaskInstanceResponse', true);
	}
	
	/**
	 * Get the binary data for a historic task instance variable
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N15962 Get the binary data for a historic task instance variable}
	 */
	public function getTheBinaryDataForHistoricTaskInstanceVariableByName($taskId, $variableName)
	{
		$data = array();
		
		return $this->client->request("history/historic-task-instances/$taskId/variables/$variableName/data", 'GET', $data, array(200), array(404 => "Indicates the requested task instance was not found or the process instance doesn't have a variable with the given name or the variable doesn't have a binary stream available. Status message provides additional information."));
	}
	
	/**
	 * Get historic activity instances
	 * 
	 * @return ActivitiGetHistoricActivityInstancesResponse
	 * @see {@link http://www.activiti.org/userguide/#restHistoricActivityInstancesGet Get historic activity instances}
	 */
	public function getHistoricActivityInstances()
	{
		$data = array();
		
		return $this->client->request("history/historic-activity-instances", 'GET', $data, array(200), array(400 => "Indicates an parameter was passed in the wrong format. The status-message contains additional information."), 'ActivitiGetHistoricActivityInstancesResponse');
	}
	
	/**
	 * Query for historic activity instances
	 * 
	 * @return ActivitiQueryForHistoricActivityInstancesResponse
	 * @see {@link http://www.activiti.org/userguide/#N15A52 Query for historic activity instances}
	 */
	public function queryForHistoricActivityInstances($processDefinitionId = null)
	{
		$data = array();
		if(!is_null($processDefinitionId))
			$data['processDefinitionId'] = $processDefinitionId;
		
		return $this->client->request("query/historic-activity-instances", 'POST', $data, array(200), array(400 => "Indicates an parameter was passed in the wrong format. The status-message contains additional information."), 'ActivitiQueryForHistoricActivityInstancesResponse');
	}
	
	/**
	 * List of historic variable instances
	 * 
	 * @return ActivitiListOfHistoricVariableInstancesResponse
	 * @see {@link http://www.activiti.org/userguide/#restHistoricVariableInstancesGet List of historic variable instances}
	 */
	public function listOfHistoricVariableInstances()
	{
		$data = array();
		
		return $this->client->request("history/historic-variable-instances", 'GET', $data, array(200), array(400 => "Indicates an parameter was passed in the wrong format. The status-message contains additional information."), 'ActivitiListOfHistoricVariableInstancesResponse');
	}
	
	/**
	 * Query for historic variable instances
	 * 
	 * @return ActivitiQueryForHistoricVariableInstancesResponse
	 * @see {@link http://www.activiti.org/userguide/#N15B00 Query for historic variable instances}
	 */
	public function queryForHistoricVariableInstances($processDefinitionId = null, $variables = null)
	{
		$data = array();
		if(!is_null($processDefinitionId))
			$data['processDefinitionId'] = $processDefinitionId;
		if(!is_null($variables))
			$data['variables'] = $variables;
		
		return $this->client->request("query/historic-variable-instances", 'POST', $data, array(200), array(400 => "Indicates an parameter was passed in the wrong format. The status-message contains additional information."), 'ActivitiQueryForHistoricVariableInstancesResponse');
	}
	
	/**
	 * Get the binary data for a historic task instance variable
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N15B40 Get the binary data for a historic task instance variable}
	 */
	public function getTheBinaryDataForHistoricTaskInstanceVariable($varInstanceId)
	{
		$data = array();
		
		return $this->client->request("history/historic-variable-instances/$varInstanceId/data", 'GET', $data, array(200), array(404 => "Indicates the requested variable instance was not found or the variable instance doesn't have a variable with the given name or the variable doesn't have a binary stream available. Status message provides additional information."));
	}
	
	/**
	 * Get historic detail
	 * 
	 * @return ActivitiGetHistoricDetailResponse
	 * @see {@link http://www.activiti.org/userguide/#restHistoricDetailGet Get historic detail}
	 */
	public function getHistoricDetail()
	{
		$data = array();
		
		return $this->client->request("history/historic-detail", 'GET', $data, array(200), array(400 => "Indicates an parameter was passed in the wrong format. The status-message contains additional information."), 'ActivitiGetHistoricDetailResponse');
	}
	
	/**
	 * Query for historic details
	 * 
	 * @return ActivitiQueryForHistoricDetailsResponse
	 * @see {@link http://www.activiti.org/userguide/#N15BFA Query for historic details}
	 */
	public function queryForHistoricDetails($processInstanceId = null)
	{
		$data = array();
		if(!is_null($processInstanceId))
			$data['processInstanceId'] = $processInstanceId;
		
		return $this->client->request("query/historic-detail", 'POST', $data, array(200), array(400 => "Indicates an parameter was passed in the wrong format. The status-message contains additional information."), 'ActivitiQueryForHistoricDetailsResponse');
	}
	
	/**
	 * Get the binary data for a historic detail variable
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N15C33 Get the binary data for a historic detail variable}
	 */
	public function getTheBinaryDataForHistoricDetailVariable($detailId)
	{
		$data = array();
		
		return $this->client->request("history/historic-detail/$detailId/data", 'GET', $data, array(200), array(404 => "Indicates the requested historic detail instance was not found or the historic detail instance doesn't have a variable with the given name or the variable doesn't have a binary stream available. Status message provides additional information."));
	}
	
}

