<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetProcessDefinitionResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'id' => 'string',
			'url' => 'string',
			'version' => 'int',
			'key' => 'string',
			'category' => 'string',
			'suspended' => 'boolean',
			'name' => 'string',
			'description' => 'string',
			'deploymentId' => 'string',
			'deploymentUrl' => 'string',
			'graphicalNotationDefined' => 'boolean',
			'resource' => 'string',
			'diagramResource' => 'string',
			'startFormDefined' => 'boolean',
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
	 * @var int
	 */
	protected $version;

	/**
	 * @var string
	 */
	protected $key;

	/**
	 * @var string
	 */
	protected $category;

	/**
	 * @var boolean
	 */
	protected $suspended;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var string
	 */
	protected $deploymentId;

	/**
	 * @var string
	 */
	protected $deploymentUrl;

	/**
	 * @var boolean
	 */
	protected $graphicalNotationDefined;

	/**
	 * @var string
	 */
	protected $resource;

	/**
	 * @var string
	 */
	protected $diagramResource;

	/**
	 * @var boolean
	 */
	protected $startFormDefined;

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
	 * @return int
	 */
	public function getVersion()
	{
		return $this->version;
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
	 * @return boolean
	 */
	public function getSuspended()
	{
		return $this->suspended;
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
	public function getDescription()
	{
		return $this->description;
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
	public function getDeploymenturl()
	{
		return $this->deploymentUrl;
	}

	/**
	 * @return boolean
	 */
	public function getGraphicalnotationdefined()
	{
		return $this->graphicalNotationDefined;
	}

	/**
	 * @return string
	 */
	public function getResource()
	{
		return $this->resource;
	}

	/**
	 * @return string
	 */
	public function getDiagramresource()
	{
		return $this->diagramResource;
	}

	/**
	 * @return boolean
	 */
	public function getStartformdefined()
	{
		return $this->startFormDefined;
	}

}

