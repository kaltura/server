<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiDeleteCandidateStarterFromProcessDefinitionResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'url' => 'string',
			'user' => 'string',
			'group' => '',
			'type' => 'string',
		));
	}
	
	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var string
	 */
	protected $user;

	/**
	 * @var 
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
	 * @return string
	 */
	public function getUser()
	{
		return $this->user;
	}

	/**
	 * @return 
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

