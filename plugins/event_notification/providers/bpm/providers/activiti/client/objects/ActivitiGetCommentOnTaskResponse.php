<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetCommentOnTaskResponse extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'id' => 'string',
			'taskUrl' => 'string',
			'processInstanceUrl' => 'string',
			'message' => 'string',
			'author' => 'string',
			'time' => 'string',
			'taskId' => 'string',
			'processInstanceId' => 'string',
		));
	}
	
	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $taskUrl;

	/**
	 * @var string
	 */
	protected $processInstanceUrl;

	/**
	 * @var string
	 */
	protected $message;

	/**
	 * @var string
	 */
	protected $author;

	/**
	 * @var string
	 */
	protected $time;

	/**
	 * @var string
	 */
	protected $taskId;

	/**
	 * @var string
	 */
	protected $processInstanceId;

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
	public function getTaskurl()
	{
		return $this->taskUrl;
	}

	/**
	 * @return string
	 */
	public function getProcessinstanceurl()
	{
		return $this->processInstanceUrl;
	}

	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}

	/**
	 * @return string
	 */
	public function getAuthor()
	{
		return $this->author;
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
	public function getTaskid()
	{
		return $this->taskId;
	}

	/**
	 * @return string
	 */
	public function getProcessinstanceid()
	{
		return $this->processInstanceId;
	}

}

