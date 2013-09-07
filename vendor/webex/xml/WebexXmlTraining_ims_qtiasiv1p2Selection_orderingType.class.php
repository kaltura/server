<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlTraining_ims_qtiasiv1p2Selection_orderingType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlQtiasiQticommentType
	 */
	protected $qticomment;
	
	/**
	 *
	 * @var WebexXmlQtiasiSequence_parameterType
	 */
	protected $sequence_parameter;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlQtiasiSelectionType>
	 */
	protected $selection;
	
	/**
	 *
	 * @var WebexXmlQtiasiOrderType
	 */
	protected $order;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'qticomment':
				return 'WebexXmlQtiasiQticommentType';
	
			case 'sequence_parameter':
				return 'WebexXmlQtiasiSequence_parameterType';
	
			case 'selection':
				return 'WebexXmlArray<WebexXmlQtiasiSelectionType>';
	
			case 'order':
				return 'WebexXmlQtiasiOrderType';
	
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
			'sequence_parameter',
			'selection',
			'order',
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
		return 'selection_orderingType';
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
	 * @param WebexXmlQtiasiSequence_parameterType $sequence_parameter
	 */
	public function setSequence_parameter(WebexXmlQtiasiSequence_parameterType $sequence_parameter)
	{
		$this->sequence_parameter = $sequence_parameter;
	}
	
	/**
	 * @return WebexXmlQtiasiSequence_parameterType $sequence_parameter
	 */
	public function getSequence_parameter()
	{
		return $this->sequence_parameter;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlQtiasiSelectionType> $selection
	 */
	public function setSelection(WebexXmlArray $selection)
	{
		if($selection->getType() != 'WebexXmlQtiasiSelectionType')
			throw new WebexXmlException(get_class($this) . "::selection must be of type WebexXmlQtiasiSelectionType");
		
		$this->selection = $selection;
	}
	
	/**
	 * @return WebexXmlArray $selection
	 */
	public function getSelection()
	{
		return $this->selection;
	}
	
	/**
	 * @param WebexXmlQtiasiOrderType $order
	 */
	public function setOrder(WebexXmlQtiasiOrderType $order)
	{
		$this->order = $order;
	}
	
	/**
	 * @return WebexXmlQtiasiOrderType $order
	 */
	public function getOrder()
	{
		return $this->order;
	}
	
}
		
