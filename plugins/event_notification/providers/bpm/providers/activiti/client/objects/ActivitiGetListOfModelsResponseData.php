<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetListOfModelsResponseData extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'name' => 'string',
			'key' => 'string',
			'category' => 'string',
			'version' => 'int',
			'metaInfo' => 'string',
			'deploymentId' => 'string',
			'id' => 'string',
			'url' => 'string',
			'createTime' => 'string',
			'lastUpdateTime' => 'string',
			'deploymentUrl' => 'string',
			'tenantId' => '',
		));
	}
	
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $key;

	/**
	 * @var string
	 */
	protected $category;

	/**
	 * @var int
	 */
	protected $version;

	/**
	 * @var string
	 */
	protected $metaInfo;

	/**
	 * @var string
	 */
	protected $deploymentId;

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
	protected $createTime;

	/**
	 * @var string
	 */
	protected $lastUpdateTime;

	/**
	 * @var string
	 */
	protected $deploymentUrl;

	/**
	 * @var 
	 */
	protected $tenantId;

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
	public function getKey()
	{
		return $this->key;
	}

	/**
	 * @return string
	 */
	public function getCategory()
	{
		return $this->category;
	}

	/**
	 * @return int
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * @return string
	 */
	public function getMetainfo()
	{
		return $this->metaInfo;
	}

	/**
	 * @return string
	 */
	public function getDeploymentid()
	{
		return $this->deploymentId;
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
	public function getUrl()
	{
		return $this->url;
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
	public function getLastupdatetime()
	{
		return $this->lastUpdateTime;
	}

	/**
	 * @return string
	 */
	public function getDeploymenturl()
	{
		return $this->deploymentUrl;
	}

	/**
	 * @return 
	 */
	public function getTenantid()
	{
		return $this->tenantId;
	}

}

