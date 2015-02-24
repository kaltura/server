<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetAllIdentityLinksForTaskResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'userId' => 'string',
			'groupId' => '',
			'type' => 'string',
			'url' => 'string',
		));
	}
	
	/**
	 * @var string
	 */
	protected $userId;

	/**
	 * @var 
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
	 * @return string
	 */
	public function getUserid()
	{
		return $this->userId;
	}

	/**
	 * @return 
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

