<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2SolutionType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiQticommentType
	 */
	protected $qticomment;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiasiSolutionmaterialType>
	 */
	protected $solutionmaterial;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'qticomment':
				return 'WebexXmlQtiasiQticommentType';
	
			case 'solutionmaterial':
				return 'WebexXmlArray<WebexXmlQtiasiSolutionmaterialType>';
	
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
			'solutionmaterial',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'solutionmaterial',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'solutionType';
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
	 * @param WebexXmlArray<WebexXmlQtiasiSolutionmaterialType> $solutionmaterial
	 */
	public function setSolutionmaterial(WebexXmlArray $solutionmaterial)
	{
		if($solutionmaterial->getType() != 'WebexXmlQtiasiSolutionmaterialType')
			throw new WebexXmlException(get_class($this) . "::solutionmaterial must be of type WebexXmlQtiasiSolutionmaterialType");
		
		$this->solutionmaterial = $solutionmaterial;
	}
	
	/**
	 * @return WebexXmlArray $solutionmaterial
	 */
	public function getSolutionmaterial()
	{
		return $this->solutionmaterial;
	}
	
}
		
