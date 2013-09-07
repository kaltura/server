<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiresv1p2Item_resultType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiAsi_metadata_item_resultType
	 */
	protected $asi_metadata;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiResponseType>
	 */
	protected $response;
	
	/**
	 *
	 * @var WebexXmlQtiOutcomes_item_resultType
	 */
	protected $outcomes;
	
	/**
	 *
	 * @var WebexXmlQtiFeedback_displayedType
	 */
	protected $feedback_displayed;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'asi_metadata':
				return 'WebexXmlQtiAsi_metadata_item_resultType';
	
			case 'response':
				return 'WebexXmlArray<WebexXmlQtiResponseType>';
	
			case 'outcomes':
				return 'WebexXmlQtiOutcomes_item_resultType';
	
			case 'feedback_displayed':
				return 'WebexXmlQtiFeedback_displayedType';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'asi_metadata',
			'response',
			'outcomes',
			'feedback_displayed',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getRequiredMembers()
	 */
	protected function getRequiredMembers()
	{
		return array(
			'response',
			'outcomes',
		);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getXmlNodeName()
	 */
	protected function getXmlNodeName()
	{
		return 'item_resultType';
	}
	
	/**
	 * @param WebexXmlQtiAsi_metadata_item_resultType $asi_metadata
	 */
	public function setAsi_metadata(WebexXmlQtiAsi_metadata_item_resultType $asi_metadata)
	{
		$this->asi_metadata = $asi_metadata;
	}
	
	/**
	 * @return WebexXmlQtiAsi_metadata_item_resultType $asi_metadata
	 */
	public function getAsi_metadata()
	{
		return $this->asi_metadata;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlQtiResponseType> $response
	 */
	public function setResponse(WebexXmlArray $response)
	{
		if($response->getType() != 'WebexXmlQtiResponseType')
			throw new WebexXmlException(get_class($this) . "::response must be of type WebexXmlQtiResponseType");
		
		$this->response = $response;
	}
	
	/**
	 * @return WebexXmlArray $response
	 */
	public function getResponse()
	{
		return $this->response;
	}
	
	/**
	 * @param WebexXmlQtiOutcomes_item_resultType $outcomes
	 */
	public function setOutcomes(WebexXmlQtiOutcomes_item_resultType $outcomes)
	{
		$this->outcomes = $outcomes;
	}
	
	/**
	 * @return WebexXmlQtiOutcomes_item_resultType $outcomes
	 */
	public function getOutcomes()
	{
		return $this->outcomes;
	}
	
	/**
	 * @param WebexXmlQtiFeedback_displayedType $feedback_displayed
	 */
	public function setFeedback_displayed(WebexXmlQtiFeedback_displayedType $feedback_displayed)
	{
		$this->feedback_displayed = $feedback_displayed;
	}
	
	/**
	 * @return WebexXmlQtiFeedback_displayedType $feedback_displayed
	 */
	public function getFeedback_displayed()
	{
		return $this->feedback_displayed;
	}
	
}
		
