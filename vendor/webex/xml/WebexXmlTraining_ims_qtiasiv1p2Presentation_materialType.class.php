<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2Presentation_materialType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiQticommentType
	 */
	protected $qticomment;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiasiFlow_matType>
	 */
	protected $flow_mat;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'qticomment':
				return 'WebexXmlQtiasiQticommentType';
	
			case 'flow_mat':
				return 'WebexXmlArray<WebexXmlQtiasiFlow_matType>';
	
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
			'flow_mat',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'flow_mat',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'presentation_materialType';
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
	 * @param WebexXmlArray<WebexXmlQtiasiFlow_matType> $flow_mat
	 */
	public function setFlow_mat(WebexXmlArray $flow_mat)
	{
		if($flow_mat->getType() != 'WebexXmlQtiasiFlow_matType')
			throw new WebexXmlException(get_class($this) . "::flow_mat must be of type WebexXmlQtiasiFlow_matType");
		
		$this->flow_mat = $flow_mat;
	}
	
	/**
	 * @return WebexXmlArray $flow_mat
	 */
	public function getFlow_mat()
	{
		return $this->flow_mat;
	}
	
}
		
