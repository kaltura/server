<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlEpOrderType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlArray<WebexXmlEpOrderByType>
	 */
	protected $orderBy;
	
	/**
	 *
	 * @var WebexXmlArray<WebexXmlServListOrderADType>
	 */
	protected $orderAD;
	
	/**
	 *
	 * @var boolean
	 */
	protected $caseSensitive;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'orderBy':
				return 'WebexXmlArray<WebexXmlEpOrderByType>';
	
			case 'orderAD':
				return 'WebexXmlArray<WebexXmlServListOrderADType>';
	
			case 'caseSensitive':
				return 'boolean';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'orderBy',
			'orderAD',
			'caseSensitive',
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
		return 'orderType';
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlEpOrderByType> $orderBy
	 */
	public function setOrderBy(WebexXmlArray $orderBy)
	{
		if($orderBy->getType() != 'WebexXmlEpOrderByType')
			throw new WebexXmlException(get_class($this) . "::orderBy must be of type WebexXmlEpOrderByType");
		
		$this->orderBy = $orderBy;
	}
	
	/**
	 * @return WebexXmlArray $orderBy
	 */
	public function getOrderBy()
	{
		return $this->orderBy;
	}
	
	/**
	 * @param WebexXmlArray<WebexXmlServListOrderADType> $orderAD
	 */
	public function setOrderAD(WebexXmlArray $orderAD)
	{
		if($orderAD->getType() != 'WebexXmlServListOrderADType')
			throw new WebexXmlException(get_class($this) . "::orderAD must be of type WebexXmlServListOrderADType");
		
		$this->orderAD = $orderAD;
	}
	
	/**
	 * @return WebexXmlArray $orderAD
	 */
	public function getOrderAD()
	{
		return $this->orderAD;
	}
	
	/**
	 * @param boolean $caseSensitive
	 */
	public function setCaseSensitive($caseSensitive)
	{
		$this->caseSensitive = $caseSensitive;
	}
	
	/**
	 * @return boolean $caseSensitive
	 */
	public function getCaseSensitive()
	{
		return $this->caseSensitive;
	}
	
}

