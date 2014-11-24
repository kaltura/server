<?php

require_once(__DIR__ . '/../ActivitiClient.php');
require_once(__DIR__ . '/../ActivitiService.php');
require_once(__DIR__ . '/../objects/ActivitiListOfProcessDefinitionsResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetProcessDefinitionResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetAllCandidateStartersForProcessdefinitionResponse.php');
require_once(__DIR__ . '/../objects/ActivitiAddCandidateStarterToProcessDefinitionResponse.php');
require_once(__DIR__ . '/../objects/ActivitiDeleteCandidateStarterFromProcessDefinitionResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetCandidateStarterFromProcessDefinitionResponse.php');
	

class ActivitiProcessDefinitionsService extends ActivitiService
{
	
	/**
	 * List of process definitions
	 * 
	 * @return ActivitiListOfProcessDefinitionsResponse
	 * @see {@link http://www.activiti.org/userguide/#N1362B List of process definitions}
	 */
	public function listOfProcessDefinitions($size = 10, $start = 0)
	{
		$data = array();
		
		return $this->client->request("repository/process-definitions?start=$start&size=$size", 'GET', $data, array(200), array(400 => "Indicates a parameter was passed in the wrong format or that 'latest' is used with other parameters other than 'key' and 'keyLike'. The status-message contains additional information."), 'ActivitiListOfProcessDefinitionsResponse');
	}
	
	/**
	 * Get a process definition
	 * 
	 * @return ActivitiGetProcessDefinitionResponse
	 * @see {@link http://www.activiti.org/userguide/#N13710 Get a process definition}
	 */
	public function getProcessDefinition($processDefinitionId)
	{
		$data = array();
		
		return $this->client->request("repository/process-definitions/$processDefinitionId", 'GET', $data, array(200), array(404 => "Indicates the requested process definition was not found."), 'ActivitiGetProcessDefinitionResponse');
	}
	
	/**
	 * Update category for a process definition
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N13767 Update category for a process definition}
	 */
	public function updateCategoryForProcessDefinition($processDefinitionId)
	{
		$data = array();
		
		return $this->client->request("repository/process-definitions/$processDefinitionId", 'PUT', $data, array(200), array(400 => "Indicates no category was defined in the request body.",404 => "Indicates the requested process definition was not found."));
	}
	
	/**
	 * Get a process definition resource content
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N1379F Get a process definition resource content}
	 */
	public function getProcessDefinitionResourceContent($processDefinitionId)
	{
		$data = array();
		
		return $this->client->request("repository/process-definitions/$processDefinitionId/resourcedata", 'GET', $data, array(200), array());
	}
	
	/**
	 * Get a process definition BPMN model
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N137CE Get a process definition BPMN model}
	 */
	public function getProcessDefinitionBPMNModel($processDefinitionId)
	{
		$data = array();
		
		return $this->client->request("repository/process-definitions/$processDefinitionId/model", 'GET', $data, array(200), array(404 => "Indicates the requested process definition was not found."));
	}
	
	/**
	 * Suspend a process definition
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N13819 Suspend a process definition}
	 */
	public function suspendProcessDefinition($processDefinitionId)
	{
		$data = array();
		
		return $this->client->request("repository/process-definitions/$processDefinitionId", 'PUT', $data, array(200), array(404 => "Indicates the requested process definition was not found.",409 => "Indicates the requested process definition is already suspended."));
	}
	
	/**
	 * Activate a process definition
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N1387C Activate a process definition}
	 */
	public function activateProcessDefinition($processDefinitionId)
	{
		$data = array();
		
		return $this->client->request("repository/process-definitions/$processDefinitionId", 'PUT', $data, array(200), array(404 => "Indicates the requested process definition was not found.",409 => "Indicates the requested process definition is already active."));
	}
	
	/**
	 * Get all candidate starters for a process-definition
	 * 
	 * @return array<ActivitiGetAllCandidateStartersForProcessdefinitionResponse>
	 * @see {@link http://www.activiti.org/userguide/#N138B8 Get all candidate starters for a process-definition}
	 */
	public function getAllCandidateStartersForProcessdefinition($processDefinitionId)
	{
		$data = array();
		
		return $this->client->request("repository/process-definitions/$processDefinitionId/identitylinks", 'GET', $data, array(200), array(404 => "Indicates the requested process definition was not found."), 'ActivitiGetAllCandidateStartersForProcessdefinitionResponse', true);
	}
	
	/**
	 * Add a candidate starter to a process definition
	 * 
	 * @return ActivitiAddCandidateStarterToProcessDefinitionResponse
	 * @see {@link http://www.activiti.org/userguide/#N138FE Add a candidate starter to a process definition}
	 */
	public function addCandidateStarterToProcessDefinition($processDefinitionId, $user = null, $groupId = null)
	{
		$data = array();
		if(!is_null($user))
			$data['user'] = $user;
		if(!is_null($groupId))
			$data['groupId'] = $groupId;
		
		return $this->client->request("repository/process-definitions/$processDefinitionId/identitylinks", 'POST', $data, array(201), array(404 => "Indicates the requested process definition was not found."), 'ActivitiAddCandidateStarterToProcessDefinitionResponse');
	}
	
	/**
	 * Delete a candidate starter from a process definition
	 * 
	 * @return ActivitiDeleteCandidateStarterFromProcessDefinitionResponse
	 * @see {@link http://www.activiti.org/userguide/#N13956 Delete a candidate starter from a process definition}
	 */
	public function deleteCandidateStarterFromProcessDefinition($processDefinitionId, $family, $identityId)
	{
		$data = array();
		
		return $this->client->request("repository/process-definitions/$processDefinitionId/identitylinks/$family/$identityId", 'DELETE', $data, array(204), array(404 => "Indicates the requested process definition was not found or the process definition doesn't have an identity-link that matches the url."), 'ActivitiDeleteCandidateStarterFromProcessDefinitionResponse');
	}
	
	/**
	 * Get a candidate starter from a process definition
	 * 
	 * @return ActivitiGetCandidateStarterFromProcessDefinitionResponse
	 * @see {@link http://www.activiti.org/userguide/#N139B4 Get a candidate starter from a process definition}
	 */
	public function getCandidateStarterFromProcessDefinition($processDefinitionId, $family, $identityId)
	{
		$data = array();
		
		return $this->client->request("repository/process-definitions/$processDefinitionId/identitylinks/$family/$identityId", 'GET', $data, array(200), array(404 => "Indicates the requested process definition was not found or the process definition doesn't have an identity-link that matches the url."), 'ActivitiGetCandidateStarterFromProcessDefinitionResponse');
	}
	
}

