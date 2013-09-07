<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSessionEnableOptionsType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $attendeeList;
	
	/**
	 *
	 * @var boolean
	 */
	protected $javaClient;
	
	/**
	 *
	 * @var boolean
	 */
	protected $nativeClient;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'attendeeList':
				return 'boolean';
	
			case 'javaClient':
				return 'boolean';
	
			case 'nativeClient':
				return 'boolean';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'attendeeList',
			'javaClient',
			'nativeClient',
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
		return 'enableOptionsType';
	}
	
	/**
	 * @param boolean $attendeeList
	 */
	public function setAttendeeList($attendeeList)
	{
		$this->attendeeList = $attendeeList;
	}
	
	/**
	 * @return boolean $attendeeList
	 */
	public function getAttendeeList()
	{
		return $this->attendeeList;
	}
	
	/**
	 * @param boolean $javaClient
	 */
	public function setJavaClient($javaClient)
	{
		$this->javaClient = $javaClient;
	}
	
	/**
	 * @return boolean $javaClient
	 */
	public function getJavaClient()
	{
		return $this->javaClient;
	}
	
	/**
	 * @param boolean $nativeClient
	 */
	public function setNativeClient($nativeClient)
	{
		$this->nativeClient = $nativeClient;
	}
	
	/**
	 * @return boolean $nativeClient
	 */
	public function getNativeClient()
	{
		return $this->nativeClient;
	}
	
}
		
