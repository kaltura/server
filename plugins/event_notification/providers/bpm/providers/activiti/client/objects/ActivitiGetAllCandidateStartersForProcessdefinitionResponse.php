<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetAllCandidateStartersForProcessdefinitionResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'url' => 'string',
			'user' => '',
			'group' => 'string',
			'type' => 'string',
		));
	}
	
	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var 
	 */
	protected $user;

	/**
	 * @var string
	 */
	protected $group;

	/**
	 * @var string
	 */
	protected $type;

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
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @return string
	 */
	public function getGroup()
	{
		return $this->group;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

}

