<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiresv1p2Asi_metadata_assessment_resultType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiAsi_metadatafield_assessment_resultType
	 */
	protected $asi_metadatafield;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'asi_metadatafield':
				return 'WebexXmlQtiAsi_metadatafield_assessment_resultType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'asi_metadatafield',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'asi_metadatafield',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'asi_metadata_assessment_resultType';
	}
	
	/**
	 * @param WebexXmlQtiAsi_metadatafield_assessment_resultType $asi_metadatafield
	 */
	public function setAsi_metadatafield(WebexXmlQtiAsi_metadatafield_assessment_resultType $asi_metadatafield)
	{
		$this->asi_metadatafield = $asi_metadatafield;
	}
	
	/**
	 * @return WebexXmlQtiAsi_metadatafield_assessment_resultType $asi_metadatafield
	 */
	public function getAsi_metadatafield()
	{
		return $this->asi_metadatafield;
	}
	
}
		
