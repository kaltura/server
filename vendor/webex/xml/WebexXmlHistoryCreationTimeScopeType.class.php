<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlHistoryCreationTimeScopeType extends WebexXmlRequestType
{
	/**
	 *
	 * @var string
	 */
	protected $creationTimeStart;
	
	/**
	 *
	 * @var string
	 */
	protected $creationTimeEnd;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'creationTimeStart':
				return 'string';
	
			case 'creationTimeEnd':
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
			'creationTimeStart',
			'creationTimeEnd',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'creationTimeStart',
			'creationTimeEnd',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'creationTimeScopeType';
	}
	
	/**
	 * @param string $creationTimeStart
	 */
	public function setCreationTimeStart($creationTimeStart)
	{
		$this->creationTimeStart = $creationTimeStart;
	}
	
	/**
	 * @return string $creationTimeStart
	 */
	public function getCreationTimeStart()
	{
		return $this->creationTimeStart;
	}
	
	/**
	 * @param string $creationTimeEnd
	 */
	public function setCreationTimeEnd($creationTimeEnd)
	{
		$this->creationTimeEnd = $creationTimeEnd;
	}
	
	/**
	 * @return string $creationTimeEnd
	 */
	public function getCreationTimeEnd()
	{
		return $this->creationTimeEnd;
	}
	
}
		
