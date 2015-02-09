<?php

require_once(__DIR__ . '/../ActivitiClient.php');
require_once(__DIR__ . '/../ActivitiService.php');
require_once(__DIR__ . '/../objects/ActivitiGetSingleGroupResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetListOfGroupsResponse.php');
	

class ActivitiGroupsService extends ActivitiService
{
	
	/**
	 * Get a single group
	 * 
	 * @return ActivitiGetSingleGroupResponse
	 * @see {@link http://www.activiti.org/userguide/#N16488 Get a single group}
	 */
	public function getSingleGroup($groupId)
	{
		$data = array();
		
		return $this->client->request("identity/groups/$groupId", 'GET', $data, array(200), array(404 => "Indicates the requested group does not exist."), 'ActivitiGetSingleGroupResponse');
	}
	
	/**
	 * Get a list of groups
	 * 
	 * @return ActivitiGetListOfGroupsResponse
	 * @see {@link http://www.activiti.org/userguide/#N164CC Get a list of groups}
	 */
	public function getListOfGroups()
	{
		$data = array();
		
		return $this->client->request("identity/groups", 'GET', $data, array(200), array(), 'ActivitiGetListOfGroupsResponse');
	}
	
	/**
	 * Update a group
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N16547 Update a group}
	 */
	public function updateGroup($groupId)
	{
		$data = array();
		
		return $this->client->request("identity/groups/$groupId", 'PUT', $data, array(200), array(404 => "Indicates the requested group was not found.",409 => "Indicates the requested group was updated simultaneously."));
	}
	
	/**
	 * Create a group
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N1657F Create a group}
	 */
	public function createGroup()
	{
		$data = array();
		
		return $this->client->request("identity/groups", 'POST', $data, array(201), array(400 => "Indicates the id of the group was missing."));
	}
	
	/**
	 * Delete a group
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N165B2 Delete a group}
	 */
	public function deleteGroup($groupId)
	{
		$data = array();
		
		return $this->client->request("identity/groups/$groupId", 'DELETE', $data, array(204), array(404 => "Indicates the requested group was not found."));
	}
	
	/**
	 * Get members in a group
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N165EF Get members in a group}
	 */
	public function getMembersInGroup($groupId)
	{
		$data = array();
		
		return $this->client->request("identity/groups/$groupId/members", 'POST', $data, array(201), array(404 => "Indicates the userId was not included in the request body.",404 => "Indicates the requested group was not found.",409 => "Indicates the requested user is already a member of the group."));
	}
	
	/**
	 * Add a member to a group
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N165FA Add a member to a group}
	 */
	public function addMemberToGroup($groupId)
	{
		$data = array();
		
		return $this->client->request("identity/groups/$groupId/members", 'POST', $data, array(201), array(404 => "Indicates the userId was not included in the request body.",404 => "Indicates the requested group was not found.",409 => "Indicates the requested user is already a member of the group."));
	}
	
	/**
	 * Delete a member from a group
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N16653 Delete a member from a group}
	 */
	public function deleteMemberFromGroup($groupId, $userId)
	{
		$data = array();
		
		return $this->client->request("identity/groups/$groupId/members/$userId", 'DELETE', $data, array(204), array(404 => "Indicates the requested group was not found or that the user is not a member of the group. The status description contains additional information about the error."));
	}
	
}

