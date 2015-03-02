<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiQueryForTasksResponseData extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'assignee' => 'string',
			'createTime' => 'string',
			'delegationState' => 'string',
			'description' => 'string',
			'dueDate' => 'string',
			'execution' => 'string',
			'id' => 'string',
			'name' => 'string',
			'owner' => 'string',
			'parentTask' => 'string',
			'priority' => 'int',
			'processDefinition' => 'string',
			'processInstance' => 'string',
			'suspended' => 'boolean',
			'taskDefinitionKey' => 'string',
			'url' => 'string',
			'tenantId' => '',
		));
	}
	
	/**
	 * @var string
	 */
	protected $assignee;

	/**
	 * @var string
	 */
	protected $createTime;

	/**
	 * @var string
	 */
	protected $delegationState;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var string
	 */
	protected $dueDate;

	/**
	 * @var string
	 */
	protected $execution;

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $owner;

	/**
	 * @var string
	 */
	protected $parentTask;

	/**
	 * @var int
	 */
	protected $priority;

	/**
	 * @var string
	 */
	protected $processDefinition;

	/**
	 * @var string
	 */
	protected $processInstance;

	/**
	 * @var boolean
	 */
	protected $suspended;

	/**
	 * @var string
	 */
	protected $taskDefinitionKey;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var 
	 */
	protected $tenantId;

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
	public function getCreatetime()
	{
		return $this->createTime;
	}

	/**
	 * @return string
	 */
	public function getDelegationstate()
	{
		return $this->delegationState;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getDuedate()
	{
		return $this->dueDate;
	}

	/**
	 * @return string
	 */
	public function getExecution()
	{
		return $this->execution;
	}

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
	public function getName()
	{
		return $this->name;
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
	public function getParenttask()
	{
		return $this->parentTask;
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
	public function getProcessdefinition()
	{
		return $this->processDefinition;
	}

	/**
	 * @return string
	 */
	public function getProcessinstance()
	{
		return $this->processInstance;
	}

	/**
	 * @return boolean
	 */
	public function getSuspended()
	{
		return $this->suspended;
	}

	/**
	 * @return string
	 */
	public function getTaskdefinitionkey()
	{
		return $this->taskDefinitionKey;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @return 
	 */
	public function getTenantid()
	{
		return $this->tenantId;
	}

}

