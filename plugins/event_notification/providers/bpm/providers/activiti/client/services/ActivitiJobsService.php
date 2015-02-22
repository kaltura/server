<?php

require_once(__DIR__ . '/../ActivitiClient.php');
require_once(__DIR__ . '/../ActivitiService.php');
require_once(__DIR__ . '/../objects/ActivitiGetSingleJobResponse.php');
require_once(__DIR__ . '/../objects/ActivitiGetListOfJobsResponse.php');
	

class ActivitiJobsService extends ActivitiService
{
	
	/**
	 * Get a single job
	 * 
	 * @return ActivitiGetSingleJobResponse
	 * @see {@link http://www.activiti.org/userguide/#N15EEA Get a single job}
	 */
	public function getSingleJob($jobId)
	{
		$data = array();
		
		return $this->client->request("management/jobs/$jobId", 'GET', $data, array(200), array(404 => "Indicates the requested job does not exist."), 'ActivitiGetSingleJobResponse');
	}
	
	/**
	 * Delete a job
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N15F2E Delete a job}
	 */
	public function deleteJob($jobId)
	{
		$data = array();
		
		return $this->client->request("management/jobs/$jobId", 'DELETE', $data, array(204), array(404 => "Indicates the requested job was not found."));
	}
	
	/**
	 * Execute a single job
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N15F6B Execute a single job}
	 */
	public function executeSingleJob($jobId)
	{
		$data = array();
		
		return $this->client->request("management/jobs/$jobId", 'POST', $data, array(204), array(404 => "Indicates the requested job was not found.",500 => "Indicates the an exception occurred while executing the job. The status-description contains additional detail about the error. The full error-stacktrace can be fetched later on if needed."));
	}
	
	/**
	 * Get the exception stacktrace for a job
	 * 
	 * @see {@link http://www.activiti.org/userguide/#N15FB3 Get the exception stacktrace for a job}
	 */
	public function getTheExceptionStacktraceForJob($jobId)
	{
		$data = array();
		
		return $this->client->request("management/jobs/$jobId/exception-stacktrace", 'GET', $data, array(), array(404 => "Indicates the requested job was not found or the job doesn't have an exception stacktrace. Status-description contains additional information about the error."));
	}
	
	/**
	 * Get a list of jobs
	 * 
	 * @return ActivitiGetListOfJobsResponse
	 * @see {@link http://www.activiti.org/userguide/#N15FEF Get a list of jobs}
	 */
	public function getListOfJobs()
	{
		$data = array();
		
		return $this->client->request("management/jobs", 'GET', $data, array(200), array(), 'ActivitiGetListOfJobsResponse');
	}
	
}

