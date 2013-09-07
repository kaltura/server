<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2ResprocessingType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiOutcomesType
	 */
	protected $outcomes;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiasiRespconditionType>
	 */
	protected $respcondition;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'outcomes':
				return 'WebexXmlQtiasiOutcomesType';
	
			case 'respcondition':
				return 'WebexXmlArray<WebexXmlQtiasiRespconditionType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'outcomes',
			'respcondition',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'outcomes',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'resprocessingType';
	}
	
	/**
	 * @param WebexXmlQtiasiOutcomesType $outcomes
	 */
	public function setOutcomes(WebexXmlQtiasiOutcomesType $outcomes)
	{
		$this->outcomes = $outcomes;
	}
	
	/**
	 * @return WebexXmlQtiasiOutcomesType $outcomes
	 */
	public function getOutcomes()
	{
		return $this->outcomes;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlQtiasiRespconditionType> $respcondition
	 */
	public function setRespcondition(WebexXmlArray $respcondition)
	{
		if($respcondition->getType() != 'WebexXmlQtiasiRespconditionType')
			throw new WebexXmlException(get_class($this) . "::respcondition must be of type WebexXmlQtiasiRespconditionType");
		
		$this->respcondition = $respcondition;
	}
	
	/**
	 * @return WebexXmlArray $respcondition
	 */
	public function getRespcondition()
	{
		return $this->respcondition;
	}
	
}
		
