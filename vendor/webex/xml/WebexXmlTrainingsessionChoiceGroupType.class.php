<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionChoiceGroupType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $label;
	
	/**
	 *
	 * @var integer
	 */
	protected $defaultChoice;
	
	/**
	 *
	 * @var integer
	 */
	protected $index;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlTrainChoiceType>
	 */
	protected $choice;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'label':
				return 'WebexXml';
	
			case 'defaultChoice':
				return 'integer';
	
			case 'index':
				return 'integer';
	
			case 'choice':
				return 'WebexXmlArray<WebexXmlTrainChoiceType>';
	
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
			'defaultChoice',
			'index',
			'choice',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'label',
			'choice',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'choiceGroupType';
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
	 * @param integer $defaultChoice
	 */
	public function setDefaultChoice($defaultChoice)
	{
		$this->defaultChoice = $defaultChoice;
	}
	
	/**
	 * @return integer $defaultChoice
	 */
	public function getDefaultChoice()
	{
		return $this->defaultChoice;
	}
	
	/**
	 * @param integer $index
	 */
	public function setIndex($index)
	{
		$this->index = $index;
	}
	
	/**
	 * @return integer $index
	 */
	public function getIndex()
	{
		return $this->index;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlTrainChoiceType> $choice
	 */
	public function setChoice(WebexXmlArray $choice)
	{
		if($choice->getType() != 'WebexXmlTrainChoiceType')
			throw new WebexXmlException(get_class($this) . "::choice must be of type WebexXmlTrainChoiceType");
		
		$this->choice = $choice;
	}
	
	/**
	 * @return WebexXmlArray $choice
	 */
	public function getChoice()
	{
		return $this->choice;
	}
	
}
		
