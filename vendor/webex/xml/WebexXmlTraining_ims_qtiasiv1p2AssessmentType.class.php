<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2AssessmentType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiQticommentType
	 */
	protected $qticomment;
	
	/**
	 *
	 * @var WebexXml
	 */
	protected $;
	
	/**
	 *
	 * @var WebexXmlQtiasiSectionType
	 */
	protected $section;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'qticomment':
				return 'WebexXmlQtiasiQticommentType';
	
			case '':
				return 'WebexXml';
	
			case 'section':
				return 'WebexXmlQtiasiSectionType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'qticomment',
			'',
			'section',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'section',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'assessmentType';
	}
	
	/**
	 * @param WebexXmlQtiasiQticommentType $qticomment
	 */
	public function setQticomment(WebexXmlQtiasiQticommentType $qticomment)
	{
		$this->qticomment = $qticomment;
	}
	
	/**
	 * @return WebexXmlQtiasiQticommentType $qticomment
	 */
	public function getQticomment()
	{
		return $this->qticomment;
	}
	
	/**
	 * @param WebexXml $
	 */
	public function set(WebexXml $)
	{
		$this-> = $;
	}
	
	/**
	 * @return WebexXml $
	 */
	public function get()
	{
		return $this->;
	}
	
	/**
	 * @param WebexXmlQtiasiSectionType $section
	 */
	public function setSection(WebexXmlQtiasiSectionType $section)
	{
		$this->section = $section;
	}
	
	/**
	 * @return WebexXmlQtiasiSectionType $section
	 */
	public function getSection()
	{
		return $this->section;
	}
	
}
		
