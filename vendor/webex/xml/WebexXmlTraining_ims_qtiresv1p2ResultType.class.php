<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiresv1p2ResultType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiContextType
	 */
	protected $context;
	
	/**
	 *
	 * @var WebexXmlQtiAssessment_resultType
	 */
	protected $assessment_result;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'context':
				return 'WebexXmlQtiContextType';
	
			case 'assessment_result':
				return 'WebexXmlQtiAssessment_resultType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'context',
			'assessment_result',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'context',
			'assessment_result',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'resultType';
	}
	
	/**
	 * @param WebexXmlQtiContextType $context
	 */
	public function setContext(WebexXmlQtiContextType $context)
	{
		$this->context = $context;
	}
	
	/**
	 * @return WebexXmlQtiContextType $context
	 */
	public function getContext()
	{
		return $this->context;
	}
	
	/**
	 * @param WebexXmlQtiAssessment_resultType $assessment_result
	 */
	public function setAssessment_result(WebexXmlQtiAssessment_resultType $assessment_result)
	{
		$this->assessment_result = $assessment_result;
	}
	
	/**
	 * @return WebexXmlQtiAssessment_resultType $assessment_result
	 */
	public function getAssessment_result()
	{
		return $this->assessment_result;
	}
	
}
		
