<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2HintType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiQticommentType
	 */
	protected $qticomment;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiasiHintmaterialType>
	 */
	protected $hintmaterial;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'qticomment':
				return 'WebexXmlQtiasiQticommentType';
	
			case 'hintmaterial':
				return 'WebexXmlArray<WebexXmlQtiasiHintmaterialType>';
	
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
			'hintmaterial',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'hintmaterial',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'hintType';
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
	 * @param WebexXmlArray<WebexXmlQtiasiHintmaterialType> $hintmaterial
	 */
	public function setHintmaterial(WebexXmlArray $hintmaterial)
	{
		if($hintmaterial->getType() != 'WebexXmlQtiasiHintmaterialType')
			throw new WebexXmlException(get_class($this) . "::hintmaterial must be of type WebexXmlQtiasiHintmaterialType");
		
		$this->hintmaterial = $hintmaterial;
	}
	
	/**
	 * @return WebexXmlArray $hintmaterial
	 */
	public function getHintmaterial()
	{
		return $this->hintmaterial;
	}
	
}
		
