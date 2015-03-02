<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiListOfProcessInstancesResponseData extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'id' => 'string',
			'url' => 'string',
			'businessKey' => 'string',
			'suspended' => 'boolean',
			'processDefinitionUrl' => 'string',
			'activityId' => 'string',
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
	protected $businessKey;

	/**
	 * @var boolean
	 */
	protected $suspended;

	/**
	 * @var string
	 */
	protected $processDefinitionUrl;

	/**
	 * @var string
	 */
	protected $activityId;

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
	public function getBusinesskey()
	{
		return $this->businessKey;
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
	public function getProcessdefinitionurl()
	{
		return $this->processDefinitionUrl;
	}

	/**
	 * @return string
	 */
	public function getActivityid()
	{
		return $this->activityId;
	}

	/**
	 * @return 
	 */
	public function getTenantid()
	{
		return $this->tenantId;
	}

}

