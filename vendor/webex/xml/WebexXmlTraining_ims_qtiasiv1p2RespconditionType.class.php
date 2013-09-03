<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2RespconditionType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiConditionvarType
	 */
	protected $conditionvar;
	
	/**
	 *
	 * @var WebexXmlQtiasiSetvarType
	 */
	protected $setvar;
	
	/**
	 *
	 * @var WebexXmlQtiasiDisplayfeedbackType
	 */
	protected $displayfeedback;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'conditionvar':
				return 'WebexXmlQtiasiConditionvarType';
	
			case 'setvar':
				return 'WebexXmlQtiasiSetvarType';
	
			case 'displayfeedback':
				return 'WebexXmlQtiasiDisplayfeedbackType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'conditionvar',
			'setvar',
			'displayfeedback',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'conditionvar',
			'setvar',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'respconditionType';
	}
	
	/**
	 * @param WebexXmlQtiasiConditionvarType $conditionvar
	 */
	public function setConditionvar(WebexXmlQtiasiConditionvarType $conditionvar)
	{
		$this->conditionvar = $conditionvar;
	}
	
	/**
	 * @return WebexXmlQtiasiConditionvarType $conditionvar
	 */
	public function getConditionvar()
	{
		return $this->conditionvar;
	}
	
	/**
	 * @param WebexXmlQtiasiSetvarType $setvar
	 */
	public function setSetvar(WebexXmlQtiasiSetvarType $setvar)
	{
		$this->setvar = $setvar;
	}
	
	/**
	 * @return WebexXmlQtiasiSetvarType $setvar
	 */
	public function getSetvar()
	{
		return $this->setvar;
	}
	
	/**
	 * @param WebexXmlQtiasiDisplayfeedbackType $displayfeedback
	 */
	public function setDisplayfeedback(WebexXmlQtiasiDisplayfeedbackType $displayfeedback)
	{
		$this->displayfeedback = $displayfeedback;
	}
	
	/**
	 * @return WebexXmlQtiasiDisplayfeedbackType $displayfeedback
	 */
	public function getDisplayfeedback()
	{
		return $this->displayfeedback;
	}
	
}
		
