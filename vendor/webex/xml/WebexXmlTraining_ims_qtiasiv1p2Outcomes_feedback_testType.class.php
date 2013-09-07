<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2Outcomes_feedback_testType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiTest_variableType
	 */
	protected $test_variable;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiasiDisplayfeedbackType>
	 */
	protected $displayfeedback;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'test_variable':
				return 'WebexXmlQtiasiTest_variableType';
	
			case 'displayfeedback':
				return 'WebexXmlArray<WebexXmlQtiasiDisplayfeedbackType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'test_variable',
			'displayfeedback',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'test_variable',
			'displayfeedback',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'outcomes_feedback_testType';
	}
	
	/**
	 * @param WebexXmlQtiasiTest_variableType $test_variable
	 */
	public function setTest_variable(WebexXmlQtiasiTest_variableType $test_variable)
	{
		$this->test_variable = $test_variable;
	}
	
	/**
	 * @return WebexXmlQtiasiTest_variableType $test_variable
	 */
	public function getTest_variable()
	{
		return $this->test_variable;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlQtiasiDisplayfeedbackType> $displayfeedback
	 */
	public function setDisplayfeedback(WebexXmlArray $displayfeedback)
	{
		if($displayfeedback->getType() != 'WebexXmlQtiasiDisplayfeedbackType')
			throw new WebexXmlException(get_class($this) . "::displayfeedback must be of type WebexXmlQtiasiDisplayfeedbackType");
		
		$this->displayfeedback = $displayfeedback;
	}
	
	/**
	 * @return WebexXmlArray $displayfeedback
	 */
	public function getDisplayfeedback()
	{
		return $this->displayfeedback;
	}
	
}
		
