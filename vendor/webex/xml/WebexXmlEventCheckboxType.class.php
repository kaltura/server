<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEventCheckboxType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $label;
	
	/**
	 *
	 * @var double
	 */
	protected $score;
	
	/**
	 *
	 * @var WebexXmlEventCheckboxStateType
	 */
	protected $state;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'label':
				return 'WebexXml';
	
			case 'score':
				return 'double';
	
			case 'state':
				return 'WebexXmlEventCheckboxStateType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'label',
			'score',
			'state',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'label',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'checkboxType';
	}
	
	/**
	 * @param WebexXml $label
	 */
	public function setLabel(WebexXml $label)
	{
		$this->label = $label;
	}
	
	/**
	 * @return WebexXml $label
	 */
	public function getLabel()
	{
		return $this->label;
	}
	
	/**
	 * @param double $score
	 */
	public function setScore($score)
	{
		$this->score = $score;
	}
	
	/**
	 * @return double $score
	 */
	public function getScore()
	{
		return $this->score;
	}
	
	/**
	 * @param WebexXmlEventCheckboxStateType $state
	 */
	public function setState(WebexXmlEventCheckboxStateType $state)
	{
		$this->state = $state;
	}
	
	/**
	 * @return WebexXmlEventCheckboxStateType $state
	 */
	public function getState()
	{
		return $this->state;
	}
	
}
		
