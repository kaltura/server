<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2QuestestinteropType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiAssessmentType
	 */
	protected $assessment;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'assessment':
				return 'WebexXmlQtiasiAssessmentType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'assessment',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'assessment',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'questestinteropType';
	}
	
	/**
	 * @param WebexXmlQtiasiAssessmentType $assessment
	 */
	public function setAssessment(WebexXmlQtiasiAssessmentType $assessment)
	{
		$this->assessment = $assessment;
	}
	
	/**
	 * @return WebexXmlQtiasiAssessmentType $assessment
	 */
	public function getAssessment()
	{
		return $this->assessment;
	}
	
}
		
