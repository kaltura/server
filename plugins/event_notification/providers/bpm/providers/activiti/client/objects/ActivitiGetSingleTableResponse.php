<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetSingleTableResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'name' => 'string',
			'url' => 'string',
			'count' => 'int',
		));
	}
	
	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var int
	 */
	protected $count;

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
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @return int
	 */
	public function getCount()
	{
		return $this->count;
	}

}

