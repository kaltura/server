<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpRecordingBasicType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $topic;
	
	/**
	 *
	 * @var WebexXmlComListingType
	 */
	protected $listing;
	
	/**
	 *
	 * @var string
	 */
	protected $presenter;
	
	/**
	 *
	 * @var string
	 */
	protected $email;
	
	/**
	 *
	 * @var string
	 */
	protected $agenda;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'topic':
				return 'string';
	
			case 'listing':
				return 'WebexXmlComListingType';
	
			case 'presenter':
				return 'string';
	
			case 'email':
				return 'string';
	
			case 'agenda':
				return 'string';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'topic',
			'listing',
			'presenter',
			'email',
			'agenda',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'recordingBasicType';
	}
	
	/**
	 * @param string $topic
	 */
	public function setTopic($topic)
	{
		$this->topic = $topic;
	}
	
	/**
	 * @return string $topic
	 */
	public function getTopic()
	{
		return $this->topic;
	}
	
	/**
	 * @param WebexXmlComListingType $listing
	 */
	public function setListing(WebexXmlComListingType $listing)
	{
		$this->listing = $listing;
	}
	
	/**
	 * @return WebexXmlComListingType $listing
	 */
	public function getListing()
	{
		return $this->listing;
	}
	
	/**
	 * @param string $presenter
	 */
	public function setPresenter($presenter)
	{
		$this->presenter = $presenter;
	}
	
	/**
	 * @return string $presenter
	 */
	public function getPresenter()
	{
		return $this->presenter;
	}
	
	/**
	 * @param string $email
	 */
	public function setEmail($email)
	{
		$this->email = $email;
	}
	
	/**
	 * @return string $email
	 */
	public function getEmail()
	{
		return $this->email;
	}
	
	/**
	 * @param string $agenda
	 */
	public function setAgenda($agenda)
	{
		$this->agenda = $agenda;
	}
	
	/**
	 * @return string $agenda
	 */
	public function getAgenda()
	{
		return $this->agenda;
	}
	
}
		
