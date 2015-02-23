<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetTheIdentityLinksOfHistoricTaskInstanceResponse extends ActivitiResponseObject
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
			'taskId' => 'string',
			'taskUrl' => 'string',
			'processInstanceId' => '',
			'processInstanceUrl' => '',
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
	 * @var string
	 */
	protected $taskId;

	/**
	 * @var string
	 */
	protected $taskUrl;

	/**
	 * @var 
	 */
	protected $processInstanceId;

	/**
	 * @var 
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
	 * @return 
	 */
	public function getProcessinstanceid()
	{
		return $this->processInstanceId;
	}

	/**
	 * @return 
	 */
	public function getProcessinstanceurl()
	{
		return $this->processInstanceUrl;
	}

}

