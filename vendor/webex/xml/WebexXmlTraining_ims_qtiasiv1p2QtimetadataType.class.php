<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2QtimetadataType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiVocabularyType
	 */
	protected $vocabulary;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiasiQtimetadatafieldType>
	 */
	protected $qtimetadatafield;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'vocabulary':
				return 'WebexXmlQtiasiVocabularyType';
	
			case 'qtimetadatafield':
				return 'WebexXmlArray<WebexXmlQtiasiQtimetadatafieldType>';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'vocabulary',
			'qtimetadatafield',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'qtimetadatafield',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'qtimetadataType';
	}
	
	/**
	 * @param WebexXmlQtiasiVocabularyType $vocabulary
	 */
	public function setVocabulary(WebexXmlQtiasiVocabularyType $vocabulary)
	{
		$this->vocabulary = $vocabulary;
	}
	
	/**
	 * @return WebexXmlQtiasiVocabularyType $vocabulary
	 */
	public function getVocabulary()
	{
		return $this->vocabulary;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlQtiasiQtimetadatafieldType> $qtimetadatafield
	 */
	public function setQtimetadatafield(WebexXmlArray $qtimetadatafield)
	{
		if($qtimetadatafield->getType() != 'WebexXmlQtiasiQtimetadatafieldType')
			throw new WebexXmlException(get_class($this) . "::qtimetadatafield must be of type WebexXmlQtiasiQtimetadatafieldType");
		
		$this->qtimetadatafield = $qtimetadatafield;
	}
	
	/**
	 * @return WebexXmlArray $qtimetadatafield
	 */
	public function getQtimetadatafield()
	{
		return $this->qtimetadatafield;
	}
	
}
		
