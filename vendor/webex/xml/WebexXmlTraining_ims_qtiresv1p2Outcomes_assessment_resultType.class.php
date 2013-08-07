<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiresv1p2Outcomes_assessment_resultType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiScore_assessment_result_outcomesType
	 */
	protected $score;
	
	/**
	 *
	 * @var WebexXmlQtiGradeType
	 */
	protected $grade;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'score':
				return 'WebexXmlQtiScore_assessment_result_outcomesType';
	
			case 'grade':
				return 'WebexXmlQtiGradeType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'score',
			'grade',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'score',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'outcomes_assessment_resultType';
	}
	
	/**
	 * @param WebexXmlQtiScore_assessment_result_outcomesType $score
	 */
	public function setScore(WebexXmlQtiScore_assessment_result_outcomesType $score)
	{
		$this->score = $score;
	}
	
	/**
	 * @return WebexXmlQtiScore_assessment_result_outcomesType $score
	 */
	public function getScore()
	{
		return $this->score;
	}
	
	/**
	 * @param WebexXmlQtiGradeType $grade
	 */
	public function setGrade(WebexXmlQtiGradeType $grade)
	{
		$this->grade = $grade;
	}
	
	/**
	 * @return WebexXmlQtiGradeType $grade
	 */
	public function getGrade()
	{
		return $this->grade;
	}
	
}
		
