<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlServOrderType extends WebexXmlRequestType
{
	/**
	 *
	 * @var WebexXmlServOrderByType
	 */
	protected $orderBy;
	
	/**
	 *
	 * @var WebexXmlServListOrderADType
	 */
	protected $orderAD;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'orderBy':
				return 'WebexXmlServOrderByType';
	
			case 'orderAD':
				return 'WebexXmlServListOrderADType';
	
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
	 * @param WebexXmlServOrderByType $orderBy
	 */
	public function setOrderBy(WebexXmlServOrderByType $orderBy)
	{
		$this->orderBy = $orderBy;
	}
	
	/**
	 * @return WebexXmlServOrderByType $orderBy
	 */
	public function getOrderBy()
	{
		return $this->orderBy;
	}
	
	/**
	 * @param WebexXmlServListOrderADType $orderAD
	 */
	public function setOrderAD(WebexXmlServListOrderADType $orderAD)
	{
		$this->orderAD = $orderAD;
	}
	
	/**
	 * @return WebexXmlServListOrderADType $orderAD
	 */
	public function getOrderAD()
	{
		return $this->orderAD;
	}
	
}
		
