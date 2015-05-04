<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetEngineInfoResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'name' => 'string',
			'version' => 'string',
			'resourceUrl' => 'string',
			'exception' => '',
		));
	}
	
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $version;

	/**
	 * @var string
	 */
	protected $resourceUrl;

	/**
	 * @var 
	 */
	protected $exception;

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
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * @return string
	 */
	public function getResourceurl()
	{
		return $this->resourceUrl;
	}

	/**
	 * @return 
	 */
	public function getException()
	{
		return $this->exception;
	}

}

