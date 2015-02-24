<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiCreateAnIdentityLinkOnTaskResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'userId' => '',
			'groupId' => 'string',
			'type' => 'string',
			'url' => 'string',
		));
	}
	
	/**
	 * @var 
	 */
	protected $userId;

	/**
	 * @var string
	 */
	protected $groupId;

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @return 
	 */
	public function getUserid()
	{
		return $this->userId;
	}

	/**
	 * @return string
	 */
	public function getGroupid()
	{
		return $this->groupId;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

}

