<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');
require_once(__DIR__ . '/ActivitiQueryForHistoricProcessInstancesResponseDataVariable.php');
	

class ActivitiQueryForHistoricProcessInstancesResponseData extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'id' => 'string',
			'businessKey' => 'string',
			'processDefinitionId' => 'string',
			'processDefinitionUrl' => 'string',
			'startTime' => 'string',
			'endTime' => 'string',
			'durationInMillis' => 'int',
			'startUserId' => 'string',
			'startActivityId' => 'string',
			'endActivityId' => 'string',
			'deleteReason' => '',
			'superProcessInstanceId' => 'string',
			'url' => 'string',
			'variables' => 'array<ActivitiQueryForHistoricProcessInstancesResponseDataVariable>',
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
	protected $businessKey;

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
	 * @var string
	 */
	protected $startUserId;

	/**
	 * @var string
	 */
	protected $startActivityId;

	/**
	 * @var string
	 */
	protected $endActivityId;

	/**
	 * @var 
	 */
	protected $deleteReason;

	/**
	 * @var string
	 */
	protected $superProcessInstanceId;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var array<ActivitiQueryForHistoricProcessInstancesResponseDataVariable>
	 */
	protected $variables;

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
	public function getBusinesskey()
	{
		return $this->businessKey;
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
	 * @return string
	 */
	public function getStartuserid()
	{
		return $this->startUserId;
	}

	/**
	 * @return string
	 */
	public function getStartactivityid()
	{
		return $this->startActivityId;
	}

	/**
	 * @return string
	 */
	public function getEndactivityid()
	{
		return $this->endActivityId;
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
	public function getSuperprocessinstanceid()
	{
		return $this->superProcessInstanceId;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @return array<ActivitiQueryForHistoricProcessInstancesResponseDataVariable>
	 */
	public function getVariables()
	{
		return $this->variables;
	}

	/**
	 * @return 
	 */
	public function getTenantid()
	{
		return $this->tenantId;
	}

}

