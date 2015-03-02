<?php

require_once(__DIR__ . '/../ActivitiClient.php');
require_once(__DIR__ . '/../ActivitiService.php');
require_once(__DIR__ . '/../objects/ActivitiGetTaskResponse.php');
require_once(__DIR__ . '/../objects/ActivitiListOfTasksResponse.php');
require_once(__DIR__ . '/../objects/ActivitiQueryForTasksResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetAllVariablesForTaskResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetVariableFromTaskResponse.php');
require_once(__DIR__ . '/../objects/ActivitiCreateNewVariablesOnTaskResponse.php');
require_once(__DIR__ . '/../objects/ActivitiCreateNewBinaryVariableOnTaskResponse.php');
require_once(__DIR__ . '/../objects/ActivitiUpdateAnExistingVariableOnTaskResponse.php');
require_once(__DIR__ . '/../objects/ActivitiUpdatingBinaryVariableOnTaskResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetAllIdentityLinksForTaskResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetSingleIdentityLinkOnTaskResponse.php');
require_once(__DIR__ . '/../objects/ActivitiCreateAnIdentityLinkOnTaskResponse.php');
require_once(__DIR__ . '/../objects/ActivitiCreateNewCommentOnTaskResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetAllCommentsOnTaskResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetCommentOnTaskResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetAllEventsForTaskResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetAnEventOnTaskResponse.php');
require_once(__DIR__ . '/../objects/ActivitiCreateNewAttachmentOnTaskContainingLinkToAnExternalResourceResponse.php');
require_once(__DIR__ . '/../objects/ActivitiCreateNewAttachmentOnTaskWithAnAttachedFileResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetAllAttachmentsOnTaskResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetAnAttachmentOnTaskResponse.php');
	

class ActivitiTasksService extends ActivitiService
{
	
	/**
	 * Get a task
	 * 
	 * @return ActivitiGetTaskResponse
	 * @see {@link http://www.activiti.org/userguide/#N1479F Get a task}
	 */
	public function getTask($taskId)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId", 'GET', $data, array(200), array(404 => "Indicates the requested task was not found."), 'ActivitiGetTaskResponse');
	}
	
	/**
	 * List of tasks
	 * 
	 * @return ActivitiListOfTasksResponse
	 * @see {@link http://www.activiti.org/userguide/#restTasksGet List of tasks}
	 */
	public function listOfTasks()
	{
		$data = array();
		
		return $this->client->request("runtime/tasks", 'GET', $data, array(200), array(400 => "Indicates a parameter was passed in the wrong format or that 'delegationState' has an invalid value (other than 'pending' and 'resolved'). The status-message contains additional information."), 'ActivitiListOfTasksResponse');
	}
	
	/**
	 * Query for tasks
	 * 
	 * @return ActivitiQueryForTasksResponse
	 * @see {@link http://www.activiti.org/userguide/#N149DD Query for tasks}
	 */
	public function queryForTasks($name = null, $description = null, $taskVariables = null, $processInstanceVariables = null)
	{
		$data = array();
		if(!is_null($name))
			$data['name'] = $name;
		if(!is_null($description))
			$data['description'] = $description;
		if(!is_null($taskVariables))
			$data['taskVariables'] = $taskVariables;
		if(!is_null($processInstanceVariables))
			$data['processInstanceVariables'] = $processInstanceVariables;
		
		return $this->client->request("query/tasks", 'POST', $data, array(200), array(400 => "Indicates a parameter was passed in the wrong format or that 'delegationState' has an invalid value (other than 'pending' and 'resolved'). The status-message contains additional information."), 'ActivitiQueryForTasksResponse');
	}
	
	/**
	 * Update a task
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N14A20 Update a task}
	 */
	public function updateTask($taskId)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId", 'PUT', $data, array(200), array(404 => "Indicates the requested task was not found.",409 => "Indicates the requested task was updated simultaneously."));
	}
	
	/**
	 * Task actions
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N14A5B Task actions}
	 */
	public function taskActions($taskId)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId", 'POST', $data, array(200), array(400 => "When the body contains an invalid value or when the assignee is missing when the action requires it.",404 => "Indicates the requested task was not found."));
	}
	
	/**
	 * Delete a task
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N14AC3 Delete a task}
	 */
	public function deleteTask($taskId, $cascadeHistory, $deleteReason)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId?cascadeHistory=$cascadeHistory&amp;deleteReason=$deleteReason", 'DELETE', $data, array(204), array(403 => "Indicates the requested task cannot be deleted because it's part of a workflow.",404 => "Indicates the requested task was not found."));
	}
	
	/**
	 * Get all variables for a task
	 * 
	 * @return array<ActivitiGetAllVariablesForTaskResponse>
	 * @see {@link http://www.activiti.org/userguide/#N14B1A Get all variables for a task}
	 */
	public function getAllVariablesForTask($taskId, $scope)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/variables?scope=$scope", 'GET', $data, array(200), array(404 => "Indicates the requested task was not found."), 'ActivitiGetAllVariablesForTaskResponse', true);
	}
	
	/**
	 * Get a variable from a task
	 * 
	 * @return ActivitiGetVariableFromTaskResponse
	 * @see {@link http://www.activiti.org/userguide/#N14B73 Get a variable from a task}
	 */
	public function getVariableFromTask($taskId, $variableName, $scope)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/variables/$variableName?scope=$scope", 'GET', $data, array(200), array(404 => "Indicates the requested task was not found or the task doesn't have a variable with the given name (in the given scope). Status message provides additional information."), 'ActivitiGetVariableFromTaskResponse');
	}
	
	/**
	 * Get the binary data for a variable
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N14BD5 Get the binary data for a variable}
	 */
	public function getTheBinaryDataForVariable($taskId, $variableName, $scope)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/variables/$variableName/data?scope=$scope", 'GET', $data, array(200), array(404 => "Indicates the requested task was not found or the task doesn't have a variable with the given name (in the given scope) or the variable doesn't have a binary stream available. Status message provides additional information."));
	}
	
	/**
	 * Create new variables on a task
	 * 
	 * @return array<ActivitiCreateNewVariablesOnTaskResponse>
	 * @see {@link http://www.activiti.org/userguide/#N14C45 Create new variables on a task}
	 */
	public function createNewVariablesOnTask($taskId, $data = null)
	{
		$data = array();
		if(!is_null($data))
			$data['data'] = $data;
		
		return $this->client->request("runtime/tasks/$taskId/variables", 'POST', $data, array(201), array(404 => "Indicates the requested task was not found.",409 => "Indicates the task already has a variable with the given name. Use the PUT method to update the task variable instead."), 'ActivitiCreateNewVariablesOnTaskResponse', true);
	}
	
	/**
	 * Create a new binary variable on a task
	 * 
	 * @return ActivitiCreateNewBinaryVariableOnTaskResponse
	 * @see {@link http://www.activiti.org/userguide/#N14CBC Create a new binary variable on a task}
	 */
	public function createNewBinaryVariableOnTask($taskId)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/variables", 'POST', $data, array(201), array(404 => "Indicates the requested task was not found.",409 => "Indicates the task already has a variable with the given name. Use the PUT method to update the task variable instead.",415 => "Indicates the serializable data contains an object for which no class is present in the JVM running the Activiti engine and therefore cannot be deserialized."), 'ActivitiCreateNewBinaryVariableOnTaskResponse');
	}
	
	/**
	 * Update an existing variable on a task
	 * 
	 * @return ActivitiUpdateAnExistingVariableOnTaskResponse
	 * @see {@link http://www.activiti.org/userguide/#N14D32 Update an existing variable on a task}
	 */
	public function updateAnExistingVariableOnTask($taskId, $variableName, $name = null, $scope = null, $type = null, $value = null)
	{
		$data = array();
		if(!is_null($name))
			$data['name'] = $name;
		if(!is_null($scope))
			$data['scope'] = $scope;
		if(!is_null($type))
			$data['type'] = $type;
		if(!is_null($value))
			$data['value'] = $value;
		
		return $this->client->request("runtime/tasks/$taskId/variables/$variableName", 'PUT', $data, array(200), array(404 => "Indicates the requested task was not found or the task doesn't have a variable with the given name in the given scope. Status message contains additional information about the error."), 'ActivitiUpdateAnExistingVariableOnTaskResponse');
	}
	
	/**
	 * Updating a binary variable on a task
	 * 
	 * @return ActivitiUpdatingBinaryVariableOnTaskResponse
	 * @see {@link http://www.activiti.org/userguide/#N14DAD Updating a binary variable on a task}
	 */
	public function updatingBinaryVariableOnTask($taskId, $variableName)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/variables/$variableName", 'PUT', $data, array(200), array(404 => "Indicates the requested task was not found or the variable to update doesn't exist for the given task in the given scope.",415 => "Indicates the serializable data contains an object for which no class is present in the JVM running the Activiti engine and therefore cannot be deserialized."), 'ActivitiUpdatingBinaryVariableOnTaskResponse');
	}
	
	/**
	 * Delete a variable on a task
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N14E27 Delete a variable on a task}
	 */
	public function deleteVariableOnTask($taskId, $variableName, $scope)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/variables/$variableName?scope=$scope", 'DELETE', $data, array(204), array(404 => "Indicates the requested task was not found or the task doesn't have a variable with the given name. Status message contains additional information about the error."));
	}
	
	/**
	 * Delete all local variables on a task
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N14E7F Delete all local variables on a task}
	 */
	public function deleteAllLocalVariablesOnTask($taskId)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/variables", 'DELETE', $data, array(204), array(404 => "Indicates the requested task was not found."));
	}
	
	/**
	 * Get all identity links for a task
	 * 
	 * @return array<ActivitiGetAllIdentityLinksForTaskResponse>
	 * @see {@link http://www.activiti.org/userguide/#N14EBC Get all identity links for a task}
	 */
	public function getAllIdentityLinksForTask($taskId)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/identitylinks", 'GET', $data, array(200), array(404 => "Indicates the requested task was not found."), 'ActivitiGetAllIdentityLinksForTaskResponse', true);
	}
	
	/**
	 * Get all identitylinks for a task for either groups or users
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N14F02 Get all identitylinks for a task for either groups or users}
	 */
	public function getAllIdentitylinksForTaskForEitherGroupsOrUsers($taskId, $taskId)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/identitylinks/users
GET runtime/tasks/$taskId/identitylinks/groups", 'GET', $data, array(200), array());
	}
	
	/**
	 * Get a single identity link on a task
	 * 
	 * @return ActivitiGetSingleIdentityLinkOnTaskResponse
	 * @see {@link http://www.activiti.org/userguide/#N14F0C Get a single identity link on a task}
	 */
	public function getSingleIdentityLinkOnTask($taskId, $family, $identityId, $type)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/identitylinks/$family/$identityId/$type", 'GET', $data, array(200), array(404 => "Indicates the requested task was not found or the task doesn't have the requested identityLink. The status contains additional information about this error."), 'ActivitiGetSingleIdentityLinkOnTaskResponse');
	}
	
	/**
	 * Create an identity link on a task
	 * 
	 * @return ActivitiCreateAnIdentityLinkOnTaskResponse
	 * @see {@link http://www.activiti.org/userguide/#N14F73 Create an identity link on a task}
	 */
	public function createAnIdentityLinkOnTask($taskId, $userId = null, $type = null, $groupId = null)
	{
		$data = array();
		if(!is_null($userId))
			$data['userId'] = $userId;
		if(!is_null($type))
			$data['type'] = $type;
		if(!is_null($groupId))
			$data['groupId'] = $groupId;
		
		return $this->client->request("runtime/tasks/$taskId/identitylinks", 'POST', $data, array(201), array(404 => "Indicates the requested task was not found or the task doesn't have the requested identityLink. The status contains additional information about this error."), 'ActivitiCreateAnIdentityLinkOnTaskResponse');
	}
	
	/**
	 * Delete an identity link on a task
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N14FCB Delete an identity link on a task}
	 */
	public function deleteAnIdentityLinkOnTask($taskId, $family, $identityId, $type)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/identitylinks/$family/$identityId/$type", 'DELETE', $data, array(204), array(404 => "Indicates the requested task was not found or the task doesn't have the requested identityLink. The status contains additional information about this error."));
	}
	
	/**
	 * Create a new comment on a task
	 * 
	 * @return ActivitiCreateNewCommentOnTaskResponse
	 * @see {@link http://www.activiti.org/userguide/#N15029 Create a new comment on a task}
	 */
	public function createNewCommentOnTask($taskId, $message = null, $saveProcessInstanceId = null)
	{
		$data = array();
		if(!is_null($message))
			$data['message'] = $message;
		if(!is_null($saveProcessInstanceId))
			$data['saveProcessInstanceId'] = $saveProcessInstanceId;
		
		return $this->client->request("runtime/tasks/$taskId/comments", 'POST', $data, array(201), array(400 => "Indicates the comment is missing from the request.",404 => "Indicates the requested task was not found."), 'ActivitiCreateNewCommentOnTaskResponse');
	}
	
	/**
	 * Get all comments on a task
	 * 
	 * @return array<ActivitiGetAllCommentsOnTaskResponse>
	 * @see {@link http://www.activiti.org/userguide/#N15083 Get all comments on a task}
	 */
	public function getAllCommentsOnTask($taskId)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/comments", 'GET', $data, array(200), array(404 => "Indicates the requested task was not found."), 'ActivitiGetAllCommentsOnTaskResponse', true);
	}
	
	/**
	 * Get a comment on a task
	 * 
	 * @return ActivitiGetCommentOnTaskResponse
	 * @see {@link http://www.activiti.org/userguide/#N150C7 Get a comment on a task}
	 */
	public function getCommentOnTask($taskId, $commentId)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/comments/$commentId", 'GET', $data, array(200), array(404 => "Indicates the requested task was not found or the tasks doesn't have a comment with the given ID."), 'ActivitiGetCommentOnTaskResponse');
	}
	
	/**
	 * Delete a comment on a task
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N15114 Delete a comment on a task}
	 */
	public function deleteCommentOnTask($taskId, $commentId)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/comments/$commentId", 'DELETE', $data, array(204), array(404 => "Indicates the requested task was not found or the tasks doesn't have a comment with the given ID."));
	}
	
	/**
	 * Get all events for a task
	 * 
	 * @return array<ActivitiGetAllEventsForTaskResponse>
	 * @see {@link http://www.activiti.org/userguide/#N15157 Get all events for a task}
	 */
	public function getAllEventsForTask($taskId)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/events", 'GET', $data, array(200), array(404 => "Indicates the requested task was not found."), 'ActivitiGetAllEventsForTaskResponse', true);
	}
	
	/**
	 * Get an event on a task
	 * 
	 * @return ActivitiGetAnEventOnTaskResponse
	 * @see {@link http://www.activiti.org/userguide/#N1519B Get an event on a task}
	 */
	public function getAnEventOnTask($taskId, $eventId)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/events/$eventId", 'GET', $data, array(200), array(404 => "Indicates the requested task was not found or the tasks doesn't have an event with the given ID."), 'ActivitiGetAnEventOnTaskResponse');
	}
	
	/**
	 * Create a new attachment on a task, containing a link to an external resource
	 * 
	 * @return ActivitiCreateNewAttachmentOnTaskContainingLinkToAnExternalResourceResponse
	 * @see {@link http://www.activiti.org/userguide/#N151E8 Create a new attachment on a task, containing a link to an external resource}
	 */
	public function createNewAttachmentOnTaskContainingLinkToAnExternalResource($taskId, $name = null, $description = null, $type = null, $externalUrl = null)
	{
		$data = array();
		if(!is_null($name))
			$data['name'] = $name;
		if(!is_null($description))
			$data['description'] = $description;
		if(!is_null($type))
			$data['type'] = $type;
		if(!is_null($externalUrl))
			$data['externalUrl'] = $externalUrl;
		
		return $this->client->request("runtime/tasks/$taskId/attachments", 'POST', $data, array(201), array(400 => "Indicates the attachment name is missing from the request.",404 => "Indicates the requested task was not found."), 'ActivitiCreateNewAttachmentOnTaskContainingLinkToAnExternalResourceResponse');
	}
	
	/**
	 * Create a new attachment on a task, with an attached file
	 * 
	 * @return ActivitiCreateNewAttachmentOnTaskWithAnAttachedFileResponse
	 * @see {@link http://www.activiti.org/userguide/#N1523A Create a new attachment on a task, with an attached file}
	 */
	public function createNewAttachmentOnTaskWithAnAttachedFile($taskId)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/attachments", 'POST', $data, array(201), array(400 => "Indicates the attachment name is missing from the request or no file was present in the request. The error-message contains additional information.",404 => "Indicates the requested task was not found."), 'ActivitiCreateNewAttachmentOnTaskWithAnAttachedFileResponse');
	}
	
	/**
	 * Get all attachments on a task
	 * 
	 * @return array<ActivitiGetAllAttachmentsOnTaskResponse>
	 * @see {@link http://www.activiti.org/userguide/#N1529D Get all attachments on a task}
	 */
	public function getAllAttachmentsOnTask($taskId)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/attachments", 'GET', $data, array(200), array(404 => "Indicates the requested task was not found."), 'ActivitiGetAllAttachmentsOnTaskResponse', true);
	}
	
	/**
	 * Get an attachment on a task
	 * 
	 * @return ActivitiGetAnAttachmentOnTaskResponse
	 * @see {@link http://www.activiti.org/userguide/#N152E1 Get an attachment on a task}
	 */
	public function getAnAttachmentOnTask($taskId, $attachmentId)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/attachments/$attachmentId", 'GET', $data, array(200), array(404 => "Indicates the requested task was not found or the tasks doesn't have a attachment with the given ID."), 'ActivitiGetAnAttachmentOnTaskResponse');
	}
	
	/**
	 * Get the content for an attachment
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N15344 Get the content for an attachment}
	 */
	public function getTheContentForAnAttachment($taskId, $attachmentId)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/attachment/$attachmentId/content", 'GET', $data, array(200), array(404 => "Indicates the requested task was not found or the task doesn't have an attachment with the given id or the attachment doesn't have a binary stream available. Status message provides additional information."));
	}
	
	/**
	 * Delete an attachment on a task
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N15396 Delete an attachment on a task}
	 */
	public function deleteAnAttachmentOnTask($taskId, $attachmentId)
	{
		$data = array();
		
		return $this->client->request("runtime/tasks/$taskId/attachments/$attachmentId", 'DELETE', $data, array(204), array(404 => "Indicates the requested task was not found or the tasks doesn't have a attachment with the given ID."));
	}
	
}

