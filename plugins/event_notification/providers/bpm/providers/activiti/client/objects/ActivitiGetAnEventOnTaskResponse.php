<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');
require_once(__DIR__ . '/ActivitiGetAnEventOnTaskResponseMessage.php');
	

class ActivitiGetAnEventOnTaskResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'action' => 'string',
			'id' => 'string',
			'message' => 'array<ActivitiGetAnEventOnTaskResponseMessage>',
			'taskUrl' => 'string',
			'time' => 'string',
			'url' => 'string',
			'userId' => '',
		));
	}
	
	/**
	 * @var string
	 */
	protected $action;

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var array<ActivitiGetAnEventOnTaskResponseMessage>
	 */
	protected $message;

	/**
	 * @var string
	 */
	protected $taskUrl;

	/**
	 * @var string
	 */
	protected $time;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var 
	 */
	protected $userId;

	/**
	 * @return string
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return array<ActivitiGetAnEventOnTaskResponseMessage>
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @return string
	 */
	public function getTaskurl()
	{
		return $this->taskUrl;
	}

	/**
	 * @return string
	 */
	public function getTime()
	{
		return $this->time;
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
	public function getUserid()
	{
		return $this->userId;
	}

}

