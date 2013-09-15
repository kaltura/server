<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTrainingsessionCheckboxGroupType extends WebexXmlRequestType
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
	protected $index;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlTrainCheckboxType>
	 */
	protected $checkbox;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'label':
				return 'WebexXml';
	
			case 'index':
				return 'integer';
	
			case 'checkbox':
				return 'WebexXmlArray<WebexXmlTrainCheckboxType>';
	
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
			'index',
			'checkbox',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'label',
			'checkbox',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'checkboxGroupType';
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
	 * @param WebexXmlArray<WebexXmlTrainCheckboxType> $checkbox
	 */
	public function setCheckbox(WebexXmlArray $checkbox)
	{
		if($checkbox->getType() != 'WebexXmlTrainCheckboxType')
			throw new WebexXmlException(get_class($this) . "::checkbox must be of type WebexXmlTrainCheckboxType");
		
		$this->checkbox = $checkbox;
	}
	
	/**
	 * @return WebexXmlArray $checkbox
	 */
	public function getCheckbox()
	{
		return $this->checkbox;
	}
	
}
		
