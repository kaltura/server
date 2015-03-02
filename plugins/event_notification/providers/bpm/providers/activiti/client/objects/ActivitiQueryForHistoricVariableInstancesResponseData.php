<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiQueryForHistoricVariableInstancesResponseData extends ActivitiResponseObject
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
			'taskId' => 'string',
			'variable' => '',
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
	protected $taskId;

	/**
	 * @var 
	 */
	protected $variable;

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
	public function getTaskid()
	{
		return $this->taskId;
	}

	/**
	 * @return 
	 */
	public function getVariable()
	{
		return $this->variable;
	}

}

