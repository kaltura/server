<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');
require_once(__DIR__ . '/ActivitiGetHistoricTaskInstancesResponseDataTaskVariable.php');
require_once(__DIR__ . '/ActivitiGetHistoricTaskInstancesResponseDataProcessVariable.php');
	

class ActivitiGetHistoricTaskInstancesResponseData extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'id' => 'string',
			'processDefinitionId' => 'string',
			'processDefinitionUrl' => 'string',
			'processInstanceId' => 'string',
			'processInstanceUrl' => 'string',
			'executionId' => 'string',
			'name' => 'string',
			'description' => 'string',
			'deleteReason' => '',
			'owner' => 'string',
			'assignee' => 'string',
			'startTime' => 'string',
			'endTime' => 'string',
			'durationInMillis' => 'int',
			'workTimeInMillis' => 'int',
			'claimTime' => 'string',
			'taskDefinitionKey' => 'string',
			'formKey' => '',
			'priority' => 'int',
			'dueDate' => 'string',
			'parentTaskId' => '',
			'url' => 'string',
			'taskVariables' => 'array<ActivitiGetHistoricTaskInstancesResponseDataTaskVariable>',
			'processVariables' => 'array<ActivitiGetHistoricTaskInstancesResponseDataProcessVariable>',
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
	protected $name;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var 
	 */
	protected $deleteReason;

	/**
	 * @var string
	 */
	protected $owner;

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
	 * @var int
	 */
	protected $workTimeInMillis;

	/**
	 * @var string
	 */
	protected $claimTime;

	/**
	 * @var string
	 */
	protected $taskDefinitionKey;

	/**
	 * @var 
	 */
	protected $formKey;

	/**
	 * @var int
	 */
	protected $priority;

	/**
	 * @var string
	 */
	protected $dueDate;

	/**
	 * @var 
	 */
	protected $parentTaskId;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var array<ActivitiGetHistoricTaskInstancesResponseDataTaskVariable>
	 */
	protected $taskVariables;

	/**
	 * @var array<ActivitiGetHistoricTaskInstancesResponseDataProcessVariable>
	 */
	protected $processVariables;

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
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @return 
	 */
	public function getDeletereason()
	{
		return $this->deleteReason;
	}

	/**
	 * @return string
	 */
	public function getOwner()
	{
		return $this->owner;
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
	 * @return int
	 */
	public function getWorktimeinmillis()
	{
		return $this->workTimeInMillis;
	}

	/**
	 * @return string
	 */
	public function getClaimtime()
	{
		return $this->claimTime;
	}

	/**
	 * @return string
	 */
	public function getTaskdefinitionkey()
	{
		return $this->taskDefinitionKey;
	}

	/**
	 * @return 
	 */
	public function getFormkey()
	{
		return $this->formKey;
	}

	/**
	 * @return int
	 */
	public function getPriority()
	{
		return $this->priority;
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
	public function getParenttaskid()
	{
		return $this->parentTaskId;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @return array<ActivitiGetHistoricTaskInstancesResponseDataTaskVariable>
	 */
	public function getTaskvariables()
	{
		return $this->taskVariables;
	}

	/**
	 * @return array<ActivitiGetHistoricTaskInstancesResponseDataProcessVariable>
	 */
	public function getProcessvariables()
	{
		return $this->processVariables;
	}

	/**
	 * @return 
	 */
	public function getTenantid()
	{
		return $this->tenantId;
	}

}

