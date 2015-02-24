<?php

require_once(__DIR__ . '/../ActivitiClient.php');
require_once(__DIR__ . '/../ActivitiService.php');
require_once(__DIR__ . '/../objects/ActivitiListOfDeploymentsResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetDeploymentResponse.php');
require_once(__DIR__ . '/../objects/ActivitiCreateNewDeploymentResponse.php');
require_once(__DIR__ . '/../objects/ActivitiListResourcesInDeploymentResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetDeploymentResourceResponse.php');
	

class ActivitiDeploymentService extends ActivitiService
{
	
	/**
	 * List of Deployments
	 * 
	 * @return ActivitiListOfDeploymentsResponse
	 * @see {@link http://www.activiti.org/userguide/#N1339E List of Deployments}
	 */
	public function listOfDeployments()
	{
		$data = array();
		
		return $this->client->request("repository/deployments", 'GET', $data, array(200), array(), 'ActivitiListOfDeploymentsResponse');
	}
	
	/**
	 * Get a deployment
	 * 
	 * @return ActivitiGetDeploymentResponse
	 * @see {@link http://www.activiti.org/userguide/#N13439 Get a deployment}
	 */
	public function getDeployment($deploymentId)
	{
		$data = array();
		
		return $this->client->request("repository/deployments/$deploymentId", 'GET', $data, array(200), array(404 => "Indicates the requested deployment was not found."), 'ActivitiGetDeploymentResponse');
	}
	
	/**
	 * Create a new deployment
	 * 
	 * @return ActivitiCreateNewDeploymentResponse
	 * @see {@link http://www.activiti.org/userguide/#N1347F Create a new deployment}
	 */
	public function createNewDeployment()
	{
		$data = array();
		
		return $this->client->request("repository/deployments", 'POST', $data, array(201), array(400 => "Indicates there was no content present in the request body or the content mime-type is not supported for deployment. The status-description contains additional information."), 'ActivitiCreateNewDeploymentResponse');
	}
	
	/**
	 * Delete a deployment
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N134BF Delete a deployment}
	 */
	public function deleteDeployment($deploymentId)
	{
		$data = array();
		
		return $this->client->request("repository/deployments/$deploymentId", 'DELETE', $data, array(204), array(404 => "Indicates the requested deployment was not found."));
	}
	
	/**
	 * List resources in a deployment
	 * 
	 * @return array<ActivitiListResourcesInDeploymentResponse>
	 * @see {@link http://www.activiti.org/userguide/#N134FC List resources in a deployment}
	 */
	public function listResourcesInDeployment($deploymentId)
	{
		$data = array();
		
		return $this->client->request("repository/deployments/$deploymentId/resources", 'GET', $data, array(200), array(404 => "Indicates the requested deployment was not found."), 'ActivitiListResourcesInDeploymentResponse', true);
	}
	
	/**
	 * Get a deployment resource
	 * 
	 * @return ActivitiGetDeploymentResourceResponse
	 * @see {@link http://www.activiti.org/userguide/#N13566 Get a deployment resource}
	 */
	public function getDeploymentResource($deploymentId, $resourceId)
	{
		$data = array();
		
		return $this->client->request("repository/deployments/$deploymentId/resources/$resourceId", 'GET', $data, array(200), array(404 => "Indicates the requested deployment was not found or there is no resource with the given id present in the deployment. The status-description contains additional information."), 'ActivitiGetDeploymentResourceResponse');
	}
	
	/**
	 * Get a deployment resource content
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N135D7 Get a deployment resource content}
	 */
	public function getDeploymentResourceContent($deploymentId, $resourceId)
	{
		$data = array();
		
		return $this->client->request("repository/deployments/$deploymentId/resourcedata/$resourceId", 'GET', $data, array(200), array(404 => "Indicates the requested deployment was not found or there is no resource with the given id present in the deployment. The status-description contains additional information."));
	}
	
}

