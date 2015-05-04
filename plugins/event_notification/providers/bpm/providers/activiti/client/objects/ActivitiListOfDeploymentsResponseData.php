<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiListOfDeploymentsResponseData extends ActivitiResponseObject
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
			'category' => 'string',
			'url' => 'string',
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
	protected $name;

	/**
	 * @var string
	 */
	protected $deploymentTime;

	/**
	 * @var string
	 */
	protected $category;

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
	 * @return string
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
	 * @return 
	 */
	public function getTenantid()
	{
		return $this->tenantId;
	}

}

