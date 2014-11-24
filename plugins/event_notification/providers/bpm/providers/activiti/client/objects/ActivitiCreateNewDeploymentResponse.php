<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiCreateNewDeploymentResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'id' => 'string',
			'name' => 'string',
			'deploymentTime' => 'string',
			'category' => '',
			'url' => 'string',
			'tenantId' => 'string',
		));
	}
	
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
	protected $deploymentTime;

	/**
	 * @var 
	 */
	protected $category;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var string
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
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getDeploymenttime()
	{
		return $this->deploymentTime;
	}

	/**
	 * @return 
	 */
	public function getCategory()
	{
		return $this->category;
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
	public function getTenantid()
	{
		return $this->tenantId;
	}

}

