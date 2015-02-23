<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetListOfJobsResponseData extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'id' => 'string',
			'url' => 'string',
			'processInstanceId' => 'string',
			'processInstanceUrl' => 'string',
			'processDefinitionId' => 'string',
			'processDefinitionUrl' => 'string',
			'executionId' => 'string',
			'executionUrl' => 'string',
			'retries' => 'int',
			'exceptionMessage' => 'string',
			'dueDate' => 'string',
			'tenantId' => '',
		));
	}
	
	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var string
	 */
	protected $processInstanceId;

	/**
	 * @var string
	 */
	protected $processInstanceUrl;

	/**
	 * @var string
	 */
	protected $processDefinitionId;

	/**
	 * @var string
	 */
	protected $processDefinitionUrl;

	/**
	 * @var string
	 */
	protected $executionId;

	/**
	 * @var string
	 */
	protected $executionUrl;

	/**
	 * @var int
	 */
	protected $retries;

	/**
	 * @var string
	 */
	protected $exceptionMessage;

	/**
	 * @var string
	 */
	protected $dueDate;

	/**
	 * @var 
	 */
	protected $tenantId;

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @return string
	 */
	public function getProcessinstanceid()
	{
		return $this->processInstanceId;
	}

	/**
	 * @return string
	 */
	public function getProcessinstanceurl()
	{
		return $this->processInstanceUrl;
	}

	/**
	 * @return string
	 */
	public function getProcessdefinitionid()
	{
		return $this->processDefinitionId;
	}

	/**
	 * @return string
	 */
	public function getProcessdefinitionurl()
	{
		return $this->processDefinitionUrl;
	}

	/**
	 * @return string
	 */
	public function getExecutionid()
	{
		return $this->executionId;
	}

	/**
	 * @return string
	 */
	public function getExecutionurl()
	{
		return $this->executionUrl;
	}

	/**
	 * @return int
	 */
	public function getRetries()
	{
		return $this->retries;
	}

	/**
	 * @return string
	 */
	public function getExceptionmessage()
	{
		return $this->exceptionMessage;
	}

	/**
	 * @return string
	 */
	public function getDuedate()
	{
		return $this->dueDate;
	}

	/**
	 * @return 
	 */
	public function getTenantid()
	{
		return $this->tenantId;
	}

}

