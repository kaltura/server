<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiQueryForHistoricActivityInstancesResponseData extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'id' => 'string',
			'activityId' => 'string',
			'activityName' => 'string',
			'activityType' => 'string',
			'processDefinitionId' => 'string',
			'processDefinitionUrl' => 'string',
			'processInstanceId' => 'string',
			'processInstanceUrl' => 'string',
			'executionId' => 'string',
			'taskId' => 'string',
			'calledProcessInstanceId' => '',
			'assignee' => 'string',
			'startTime' => 'string',
			'endTime' => 'string',
			'durationInMillis' => 'int',
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
	protected $activityId;

	/**
	 * @var string
	 */
	protected $activityName;

	/**
	 * @var string
	 */
	protected $activityType;

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
	protected $processInstanceId;

	/**
	 * @var string
	 */
	protected $processInstanceUrl;

	/**
	 * @var string
	 */
	protected $executionId;

	/**
	 * @var string
	 */
	protected $taskId;

	/**
	 * @var 
	 */
	protected $calledProcessInstanceId;

	/**
	 * @var string
	 */
	protected $assignee;

	/**
	 * @var string
	 */
	protected $startTime;

	/**
	 * @var string
	 */
	protected $endTime;

	/**
	 * @var int
	 */
	protected $durationInMillis;

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
	public function getActivityid()
	{
		return $this->activityId;
	}

	/**
	 * @return string
	 */
	public function getActivityname()
	{
		return $this->activityName;
	}

	/**
	 * @return string
	 */
	public function getActivitytype()
	{
		return $this->activityType;
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
	public function getExecutionid()
	{
		return $this->executionId;
	}

	/**
	 * @return string
	 */
	public function getTaskid()
	{
		return $this->taskId;
	}

	/**
	 * @return 
	 */
	public function getCalledprocessinstanceid()
	{
		return $this->calledProcessInstanceId;
	}

	/**
	 * @return string
	 */
	public function getAssignee()
	{
		return $this->assignee;
	}

	/**
	 * @return string
	 */
	public function getStarttime()
	{
		return $this->startTime;
	}

	/**
	 * @return string
	 */
	public function getEndtime()
	{
		return $this->endTime;
	}

	/**
	 * @return int
	 */
	public function getDurationinmillis()
	{
		return $this->durationInMillis;
	}

	/**
	 * @return 
	 */
	public function getTenantid()
	{
		return $this->tenantId;
	}

}

