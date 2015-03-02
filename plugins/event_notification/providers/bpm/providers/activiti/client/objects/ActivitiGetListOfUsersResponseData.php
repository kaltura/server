<?php

require_once(__DIR__ . '/../ActivitiResponseObject.php');

	

class ActivitiGetListOfUsersResponseData extends ActivitiResponseObject
{
	/* (non-PHPdoc)
	 * @see ActivitiResponseObject::getAttributes()
	 */
	protected function getAttributes()
	{
		return array_merge(parent::getAttributes(), array(
			'id' => 'string',
			'firstName' => 'string',
			'lastName' => 'string',
			'url' => 'string',
			'email' => 'string',
		));
	}
	
	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $firstName;

	/**
	 * @var string
	 */
	protected $lastName;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var string
	 */
	protected $email;

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
	public function getFirstname()
	{
		return $this->firstName;
	}

	/**
	 * @return string
	 */
	public function getLastname()
	{
		return $this->lastName;
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
	public function getEmail()
	{
		return $this->email;
	}

}

