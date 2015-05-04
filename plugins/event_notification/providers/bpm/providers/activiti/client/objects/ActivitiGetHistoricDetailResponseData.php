<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetHistoricDetailResponseData extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'id' => 'string',
			'processInstanceId' => 'string',
			'processInstanceUrl' => 'string',
			'executionId' => 'string',
			'activityInstanceId' => 'string',
			'taskId' => 'string',
			'taskUrl' => 'string',
			'time' => 'string',
			'detailType' => 'string',
			'revision' => 'int',
			'variable' => '',
			'propertyId' => '',
			'propertyValue' => '',
		));
	}
	
	/**
	 * @var string
	 */
	protected $id;

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
	protected $activityInstanceId;

	/**
	 * @var string
	 */
	protected $taskId;

	/**
	 * @var string
	 */
	protected $taskUrl;

	/**
	 * @var string
	 */
	protected $time;

	/**
	 * @var string
	 */
	protected $detailType;

	/**
	 * @var int
	 */
	protected $revision;

	/**
	 * @var 
	 */
	protected $variable;

	/**
	 * @var 
	 */
	protected $propertyId;

	/**
	 * @var 
	 */
	protected $propertyValue;

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
	public function getActivityinstanceid()
	{
		return $this->activityInstanceId;
	}

	/**
	 * @return string
	 */
	public function getTaskid()
	{
		return $this->taskId;
	}

	/**
	 * @return string
	 */
	public function getTaskurl()
	{
		return $this->taskUrl;
	}

	/**
	 * @return string
	 */
	public function getTime()
	{
		return $this->time;
	}

	/**
	 * @return string
	 */
	public function getDetailtype()
	{
		return $this->detailType;
	}

	/**
	 * @return int
	 */
	public function getRevision()
	{
		return $this->revision;
	}

	/**
	 * @return 
	 */
	public function getVariable()
	{
		return $this->variable;
	}

	/**
	 * @return 
	 */
	public function getPropertyid()
	{
		return $this->propertyId;
	}

	/**
	 * @return 
	 */
	public function getPropertyvalue()
	{
		return $this->propertyValue;
	}

}

