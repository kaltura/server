<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiListResourcesInDeploymentResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'id' => 'string',
			'url' => 'string',
			'dataUrl' => 'string',
			'mediaType' => 'string',
			'type' => 'string',
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
	protected $dataUrl;

	/**
	 * @var string
	 */
	protected $mediaType;

	/**
	 * @var string
	 */
	protected $type;

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
	public function getDataurl()
	{
		return $this->dataUrl;
	}

	/**
	 * @return string
	 */
	public function getMediatype()
	{
		return $this->mediaType;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

}

