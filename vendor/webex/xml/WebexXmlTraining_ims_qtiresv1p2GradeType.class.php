<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiresv1p2GradeType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXml
	 */
	protected $grade_value;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'grade_value':
				return 'WebexXml';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'grade_value',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'grade_value',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'gradeType';
	}
	
	/**
	 * @param WebexXml $grade_value
	 */
	public function setGrade_value(WebexXml $grade_value)
	{
		$this->grade_value = $grade_value;
	}
	
	/**
	 * @return WebexXml $grade_value
	 */
	public function getGrade_value()
	{
		return $this->grade_value;
	}
	
}
		
