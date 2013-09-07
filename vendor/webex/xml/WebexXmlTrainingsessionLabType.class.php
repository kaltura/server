<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionLabType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $labName;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $description;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'labName':
				return 'WebexXml';
	
			case 'description':
				return 'WebexXml';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'labName',
			'description',
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
		return 'labType';
	}
	
	/**
	 * @param WebexXml $labName
	 */
	public function setLabName(WebexXml $labName)
	{
		$this->labName = $labName;
	}
	
	/**
	 * @return WebexXml $labName
	 */
	public function getLabName()
	{
		return $this->labName;
	}
	
	/**
	 * @param WebexXml $description
	 */
	public function setDescription(WebexXml $description)
	{
		$this->description = $description;
	}
	
	/**
	 * @return WebexXml $description
	 */
	public function getDescription()
	{
		return $this->description;
	}
	
}
		
