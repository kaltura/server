<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2ItemType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiPresentationType
	 */
	protected $presentation;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiasiResprocessingType>
	 */
	protected $resprocessing;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiasiItemfeedbackType>
	 */
	protected $itemfeedback;
	
	/**
	 *
	 * @var WebexXmlQtiasiQticommentType
	 */
	protected $qticomment;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'presentation':
				return 'WebexXmlQtiasiPresentationType';
	
			case 'resprocessing':
				return 'WebexXmlArray<WebexXmlQtiasiResprocessingType>';
	
			case 'itemfeedback':
				return 'WebexXmlArray<WebexXmlQtiasiItemfeedbackType>';
	
			case 'qticomment':
				return 'WebexXmlQtiasiQticommentType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'presentation',
			'resprocessing',
			'itemfeedback',
			'qticomment',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'presentation',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'itemType';
	}
	
	/**
	 * @param WebexXmlQtiasiPresentationType $presentation
	 */
	public function setPresentation(WebexXmlQtiasiPresentationType $presentation)
	{
		$this->presentation = $presentation;
	}
	
	/**
	 * @return WebexXmlQtiasiPresentationType $presentation
	 */
	public function getPresentation()
	{
		return $this->presentation;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlQtiasiResprocessingType> $resprocessing
	 */
	public function setResprocessing(WebexXmlArray $resprocessing)
	{
		if($resprocessing->getType() != 'WebexXmlQtiasiResprocessingType')
			throw new WebexXmlException(get_class($this) . "::resprocessing must be of type WebexXmlQtiasiResprocessingType");
		
		$this->resprocessing = $resprocessing;
	}
	
	/**
	 * @return WebexXmlArray $resprocessing
	 */
	public function getResprocessing()
	{
		return $this->resprocessing;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlQtiasiItemfeedbackType> $itemfeedback
	 */
	public function setItemfeedback(WebexXmlArray $itemfeedback)
	{
		if($itemfeedback->getType() != 'WebexXmlQtiasiItemfeedbackType')
			throw new WebexXmlException(get_class($this) . "::itemfeedback must be of type WebexXmlQtiasiItemfeedbackType");
		
		$this->itemfeedback = $itemfeedback;
	}
	
	/**
	 * @return WebexXmlArray $itemfeedback
	 */
	public function getItemfeedback()
	{
		return $this->itemfeedback;
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
	
}
		
