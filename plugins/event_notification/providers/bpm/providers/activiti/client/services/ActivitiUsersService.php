<?php

require_once(__DIR__ . '/../ActivitiClient.php');
require_once(__DIR__ . '/../ActivitiService.php');
require_once(__DIR__ . '/../objects/ActivitiGetSingleUserResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetListOfUsersResponse.php');
	

class ActivitiUsersService extends ActivitiService
{
	
	/**
	 * Get a single user
	 * 
	 * @return ActivitiGetSingleUserResponse
	 * @see {@link http://www.activiti.org/userguide/#N160E0 Get a single user}
	 */
	public function getSingleUser($userId)
	{
		$data = array();
		
		return $this->client->request("identity/users/$userId", 'GET', $data, array(200), array(404 => "Indicates the requested user does not exist."), 'ActivitiGetSingleUserResponse');
	}
	
	/**
	 * Get a list of users
	 * 
	 * @return ActivitiGetListOfUsersResponse
	 * @see {@link http://www.activiti.org/userguide/#N16124 Get a list of users}
	 */
	public function getListOfUsers()
	{
		$data = array();
		
		return $this->client->request("identity/users", 'GET', $data, array(200), array(), 'ActivitiGetListOfUsersResponse');
	}
	
	/**
	 * Update a user
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N161BD Update a user}
	 */
	public function updateUser($userId)
	{
		$data = array();
		
		return $this->client->request("identity/users/$userId", 'PUT', $data, array(200), array(404 => "Indicates the requested user was not found.",409 => "Indicates the requested user was updated simultaneously."));
	}
	
	/**
	 * Create a user
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N161F8 Create a user}
	 */
	public function createUser()
	{
		$data = array();
		
		return $this->client->request("identity/users", 'POST', $data, array(201), array(400 => "Indicates the id of the user was missing."));
	}
	
	/**
	 * Delete a user
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N1622B Delete a user}
	 */
	public function deleteUser($userId)
	{
		$data = array();
		
		return $this->client->request("identity/users/$userId", 'DELETE', $data, array(204), array(404 => "Indicates the requested user was not found."));
	}
	
	/**
	 * Get a user's picture
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N16268 Get a user's picture}
	 */
	public function getUsersPicture($userId)
	{
		$data = array();
		
		return $this->client->request("identity/users/$userId/picture", 'GET', $data, array(200), array(404 => "Indicates the requested user was not found or the user does not have a profile picture. Status-description contains additional information about the error."));
	}
	
	/**
	 * Updating a user's picture
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N162A9 Updating a user's picture}
	 */
	public function updatingUsersPicture($userId)
	{
		$data = array();
		
		return $this->client->request("identity/users/$userId/picture", 'GET', $data, array(200), array(404 => "Indicates the requested user was not found."));
	}
	
	/**
	 * List a user's info
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N162F9 List a user's info}
	 */
	public function listUsersInfo($userId)
	{
		$data = array();
		
		return $this->client->request("identity/users/$userId/info", 'PUT', $data, array(200), array(404 => "Indicates the requested user was not found."));
	}
	
	/**
	 * Get a user's info
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N1633D Get a user's info}
	 */
	public function getUsersInfo($userId, $key)
	{
		$data = array();
		
		return $this->client->request("identity/users/$userId/info/$key", 'GET', $data, array(200), array(404 => "Indicates the requested user was not found or the user doesn't have info for the given key. Status description contains additional information about the error."));
	}
	
	/**
	 * Update a user's info
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N1638A Update a user's info}
	 */
	public function updateUsersInfo($userId, $key)
	{
		$data = array();
		
		return $this->client->request("identity/users/$userId/info/$key", 'PUT', $data, array(200), array(400 => "Indicates the value was missing from the request body.",404 => "Indicates the requested user was not found or the user doesn't have info for the given key. Status description contains additional information about the error."));
	}
	
	/**
	 * Create a new user's info entry
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N163E5 Create a new user's info entry}
	 */
	public function createNewUsersInfoEntry($userId)
	{
		$data = array();
		
		return $this->client->request("identity/users/$userId/info", 'POST', $data, array(201), array(400 => "Indicates the key or value was missing from the request body. Status description contains additional information about the error.",404 => "Indicates the requested user was not found."));
	}
	
	/**
	 * Delete a user's info
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N1643F Delete a user's info}
	 */
	public function deleteUsersInfo($userId, $key)
	{
		$data = array();
		
		return $this->client->request("identity/users/$userId/info/$key", 'DELETE', $data, array(204), array(404 => "Indicates the requested user was not found or the user doesn't have info for the given key. Status description contains additional information about the error."));
	}
	
}

