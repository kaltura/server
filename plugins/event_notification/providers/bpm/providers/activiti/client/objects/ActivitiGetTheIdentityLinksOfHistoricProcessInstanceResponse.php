<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetTheIdentityLinksOfHistoricProcessInstanceResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'type' => 'string',
			'userId' => 'string',
			'groupId' => '',
			'taskId' => '',
			'taskUrl' => '',
			'processInstanceId' => 'string',
			'processInstanceUrl' => 'string',
		));
	}
	
	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $userId;

	/**
	 * @var 
	 */
	protected $groupId;

	/**
	 * @var 
	 */
	protected $taskId;

	/**
	 * @var 
	 */
	protected $taskUrl;

	/**
	 * @var string
	 */
	protected $processInstanceId;

	/**
	 * @var string
	 */
	protected $processInstanceUrl;

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getUserid()
	{
		return $this->userId;
	}

	/**
	 * @return 
	 */
	public function getGroupid()
	{
		return $this->groupId;
	}

	/**
	 * @return 
	 */
	public function getTaskid()
	{
		return $this->taskId;
	}

	/**
	 * @return 
	 */
	public function getTaskurl()
	{
		return $this->taskUrl;
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

}

