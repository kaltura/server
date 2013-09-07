<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2ObjectbankType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiQticommentType
	 */
	protected $qticomment;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiasiQtimetadataType>
	 */
	protected $qtimetadata;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'qticomment':
				return 'WebexXmlQtiasiQticommentType';
	
			case 'qtimetadata':
				return 'WebexXmlArray<WebexXmlQtiasiQtimetadataType>';
	
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
			'qtimetadata',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'objectbankType';
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
	 * @param WebexXmlArray<WebexXmlQtiasiQtimetadataType> $qtimetadata
	 */
	public function setQtimetadata(WebexXmlArray $qtimetadata)
	{
		if($qtimetadata->getType() != 'WebexXmlQtiasiQtimetadataType')
			throw new WebexXmlException(get_class($this) . "::qtimetadata must be of type WebexXmlQtiasiQtimetadataType");
		
		$this->qtimetadata = $qtimetadata;
	}
	
	/**
	 * @return WebexXmlArray $qtimetadata
	 */
	public function getQtimetadata()
	{
		return $this->qtimetadata;
	}
	
}
		
