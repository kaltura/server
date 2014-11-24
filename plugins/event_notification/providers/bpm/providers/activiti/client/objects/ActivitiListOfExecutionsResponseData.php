<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiListOfExecutionsResponseData extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'id' => 'string',
			'url' => 'string',
			'parentId' => '',
			'parentUrl' => '',
			'processInstanceId' => 'string',
			'processInstanceUrl' => 'string',
			'suspended' => 'boolean',
			'activityId' => '',
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
	 * @var 
	 */
	protected $parentId;

	/**
	 * @var 
	 */
	protected $parentUrl;

	/**
	 * @var string
	 */
	protected $processInstanceId;

	/**
	 * @var string
	 */
	protected $processInstanceUrl;

	/**
	 * @var boolean
	 */
	protected $suspended;

	/**
	 * @var 
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
	 * @return 
	 */
	public function getParentid()
	{
		return $this->parentId;
	}

	/**
	 * @return 
	 */
	public function getParenturl()
	{
		return $this->parentUrl;
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
	 * @return boolean
	 */
	public function getSuspended()
	{
		return $this->suspended;
	}

	/**
	 * @return 
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

