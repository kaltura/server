<?php

require_once(__DIR__ . '/../ActivitiClient.php');
require_once(__DIR__ . '/../ActivitiService.php');
require_once(__DIR__ . '/../objects/ActivitiGetListOfModelsResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetModelResponse.php');
require_once(__DIR__ . '/../objects/ActivitiUpdateModelResponse.php');
require_once(__DIR__ . '/../objects/ActivitiCreateModelResponse.php');
	

class ActivitiModelsService extends ActivitiService
{
	
	/**
	 * Get a list of models
	 * 
	 * @return ActivitiGetListOfModelsResponse
	 * @see {@link http://www.activiti.org/userguide/#N13A15 Get a list of models}
	 */
	public function getListOfModels()
	{
		$data = array();
		
		return $this->client->request("repository/models", 'GET', $data, array(200), array(400 => "Indicates a parameter was passed in the wrong format. The status-message contains additional information."), 'ActivitiGetListOfModelsResponse');
	}
	
	/**
	 * Get a model
	 * 
	 * @return ActivitiGetModelResponse
	 * @see {@link http://www.activiti.org/userguide/#N13B01 Get a model}
	 */
	public function getModel($modelId)
	{
		$data = array();
		
		return $this->client->request("repository/models/$modelId", 'GET', $data, array(200), array(404 => "Indicates the requested model was not found."), 'ActivitiGetModelResponse');
	}
	
	/**
	 * Update a model
	 * 
	 * @return ActivitiUpdateModelResponse
	 * @see {@link http://www.activiti.org/userguide/#N13B47 Update a model}
	 */
	public function updateModel($modelId, $name = null, $key = null, $category = null, $version = null, $metaInfo = null, $deploymentId = null, $tenantId = null)
	{
		$data = array();
		if(!is_null($name))
			$data['name'] = $name;
		if(!is_null($key))
			$data['key'] = $key;
		if(!is_null($category))
			$data['category'] = $category;
		if(!is_null($version))
			$data['version'] = $version;
		if(!is_null($metaInfo))
			$data['metaInfo'] = $metaInfo;
		if(!is_null($deploymentId))
			$data['deploymentId'] = $deploymentId;
		if(!is_null($tenantId))
			$data['tenantId'] = $tenantId;
		
		return $this->client->request("repository/models/$modelId", 'PUT', $data, array(200), array(404 => "Indicates the requested model was not found."), 'ActivitiUpdateModelResponse');
	}
	
	/**
	 * Create a model
	 * 
	 * @return ActivitiCreateModelResponse
	 * @see {@link http://www.activiti.org/userguide/#N13B7D Create a model}
	 */
	public function createModel($name = null, $key = null, $category = null, $version = null, $metaInfo = null, $deploymentId = null, $tenantId = null)
	{
		$data = array();
		if(!is_null($name))
			$data['name'] = $name;
		if(!is_null($key))
			$data['key'] = $key;
		if(!is_null($category))
			$data['category'] = $category;
		if(!is_null($version))
			$data['version'] = $version;
		if(!is_null($metaInfo))
			$data['metaInfo'] = $metaInfo;
		if(!is_null($deploymentId))
			$data['deploymentId'] = $deploymentId;
		if(!is_null($tenantId))
			$data['tenantId'] = $tenantId;
		
		return $this->client->request("repository/models", 'POST', $data, array(201), array(), 'ActivitiCreateModelResponse');
	}
	
	/**
	 * Delete a model
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N13BAB Delete a model}
	 */
	public function deleteModel($modelId)
	{
		$data = array();
		
		return $this->client->request("repository/models/$modelId", 'DELETE', $data, array(204), array(404 => "Indicates the requested model was not found."));
	}
	
	/**
	 * Get the editor source for a model
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N13BE8 Get the editor source for a model}
	 */
	public function getTheEditorSourceForModel($modelId)
	{
		$data = array();
		
		return $this->client->request("repository/models/$modelId/source", 'GET', $data, array(200), array(404 => "Indicates the requested model was not found."));
	}
	
	/**
	 * Set the editor source for a model
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N13C2E Set the editor source for a model}
	 */
	public function setTheEditorSourceForModel($modelId)
	{
		$data = array();
		
		return $this->client->request("repository/models/$modelId/source", 'PUT', $data, array(200), array(404 => "Indicates the requested model was not found."));
	}
	
	/**
	 * Get the extra editor source for a model
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N13C7D Get the extra editor source for a model}
	 */
	public function getTheExtraEditorSourceForModel($modelId)
	{
		$data = array();
		
		return $this->client->request("repository/models/$modelId/source-extra", 'GET', $data, array(200), array(404 => "Indicates the requested model was not found."));
	}
	
	/**
	 * Set the extra editor source for a model
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N13CC3 Set the extra editor source for a model}
	 */
	public function setTheExtraEditorSourceForModel($modelId)
	{
		$data = array();
		
		return $this->client->request("repository/models/$modelId/source-extra", 'PUT', $data, array(200), array(404 => "Indicates the requested model was not found."));
	}
	
}

