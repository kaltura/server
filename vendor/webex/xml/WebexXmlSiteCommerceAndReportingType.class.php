<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteCommerceAndReportingType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $trackingCode;
	
	/**
	 *
	 * @var boolean
	 */
	protected $siteAdminReport;
	
	/**
	 *
	 * @var boolean
	 */
	protected $subScriptionService;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isECommmerce;
	
	/**
	 *
	 * @var float
	 */
	protected $conferencePrice;
	
	/**
	 *
	 * @var float
	 */
	protected $callInPrice;
	
	/**
	 *
	 * @var float
	 */
	protected $callInTollFreePrice;
	
	/**
	 *
	 * @var float
	 */
	protected $callOutPrice;
	
	/**
	 *
	 * @var float
	 */
	protected $voIPPrice;
	
	/**
	 *
	 * @var long
	 */
	protected $creditCardAuthorRetries;
	
	/**
	 *
	 * @var boolean
	 */
	protected $customereCommerce;
	
	/**
	 *
	 * @var boolean
	 */
	protected $isLocalTax;
	
	/**
	 *
	 * @var string
	 */
	protected $localTaxName;
	
	/**
	 *
	 * @var float
	 */
	protected $localTaxtRate;
	
	/**
	 *
	 * @var long
	 */
	protected $holReport;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'trackingCode':
				return 'boolean';
	
			case 'siteAdminReport':
				return 'boolean';
	
			case 'subScriptionService':
				return 'boolean';
	
			case 'isECommmerce':
				return 'boolean';
	
			case 'conferencePrice':
				return 'float';
	
			case 'callInPrice':
				return 'float';
	
			case 'callInTollFreePrice':
				return 'float';
	
			case 'callOutPrice':
				return 'float';
	
			case 'voIPPrice':
				return 'float';
	
			case 'creditCardAuthorRetries':
				return 'long';
	
			case 'customereCommerce':
				return 'boolean';
	
			case 'isLocalTax':
				return 'boolean';
	
			case 'localTaxName':
				return 'string';
	
			case 'localTaxtRate':
				return 'float';
	
			case 'holReport':
				return 'long';
	
		}
		
		return parent::getAttributeType($attributeName);
	}
	
	/* (non-PHPdoc)
	 * @see WebexXmlRequestType::getMembers()
	 */
	public function getMembers()
	{
		return array(
			'trackingCode',
			'siteAdminReport',
			'subScriptionService',
			'isECommmerce',
			'conferencePrice',
			'callInPrice',
			'callInTollFreePrice',
			'callOutPrice',
			'voIPPrice',
			'creditCardAuthorRetries',
			'customereCommerce',
			'isLocalTax',
			'localTaxName',
			'localTaxtRate',
			'holReport',
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
		return 'commerceAndReportingType';
	}
	
	/**
	 * @param boolean $trackingCode
	 */
	public function setTrackingCode($trackingCode)
	{
		$this->trackingCode = $trackingCode;
	}
	
	/**
	 * @return boolean $trackingCode
	 */
	public function getTrackingCode()
	{
		return $this->trackingCode;
	}
	
	/**
	 * @param boolean $siteAdminReport
	 */
	public function setSiteAdminReport($siteAdminReport)
	{
		$this->siteAdminReport = $siteAdminReport;
	}
	
	/**
	 * @return boolean $siteAdminReport
	 */
	public function getSiteAdminReport()
	{
		return $this->siteAdminReport;
	}
	
	/**
	 * @param boolean $subScriptionService
	 */
	public function setSubScriptionService($subScriptionService)
	{
		$this->subScriptionService = $subScriptionService;
	}
	
	/**
	 * @return boolean $subScriptionService
	 */
	public function getSubScriptionService()
	{
		return $this->subScriptionService;
	}
	
	/**
	 * @param boolean $isECommmerce
	 */
	public function setIsECommmerce($isECommmerce)
	{
		$this->isECommmerce = $isECommmerce;
	}
	
	/**
	 * @return boolean $isECommmerce
	 */
	public function getIsECommmerce()
	{
		return $this->isECommmerce;
	}
	
	/**
	 * @param float $conferencePrice
	 */
	public function setConferencePrice($conferencePrice)
	{
		$this->conferencePrice = $conferencePrice;
	}
	
	/**
	 * @return float $conferencePrice
	 */
	public function getConferencePrice()
	{
		return $this->conferencePrice;
	}
	
	/**
	 * @param float $callInPrice
	 */
	public function setCallInPrice($callInPrice)
	{
		$this->callInPrice = $callInPrice;
	}
	
	/**
	 * @return float $callInPrice
	 */
	public function getCallInPrice()
	{
		return $this->callInPrice;
	}
	
	/**
	 * @param float $callInTollFreePrice
	 */
	public function setCallInTollFreePrice($callInTollFreePrice)
	{
		$this->callInTollFreePrice = $callInTollFreePrice;
	}
	
	/**
	 * @return float $callInTollFreePrice
	 */
	public function getCallInTollFreePrice()
	{
		return $this->callInTollFreePrice;
	}
	
	/**
	 * @param float $callOutPrice
	 */
	public function setCallOutPrice($callOutPrice)
	{
		$this->callOutPrice = $callOutPrice;
	}
	
	/**
	 * @return float $callOutPrice
	 */
	public function getCallOutPrice()
	{
		return $this->callOutPrice;
	}
	
	/**
	 * @param float $voIPPrice
	 */
	public function setVoIPPrice($voIPPrice)
	{
		$this->voIPPrice = $voIPPrice;
	}
	
	/**
	 * @return float $voIPPrice
	 */
	public function getVoIPPrice()
	{
		return $this->voIPPrice;
	}
	
	/**
	 * @param long $creditCardAuthorRetries
	 */
	public function setCreditCardAuthorRetries($creditCardAuthorRetries)
	{
		$this->creditCardAuthorRetries = $creditCardAuthorRetries;
	}
	
	/**
	 * @return long $creditCardAuthorRetries
	 */
	public function getCreditCardAuthorRetries()
	{
		return $this->creditCardAuthorRetries;
	}
	
	/**
	 * @param boolean $customereCommerce
	 */
	public function setCustomereCommerce($customereCommerce)
	{
		$this->customereCommerce = $customereCommerce;
	}
	
	/**
	 * @return boolean $customereCommerce
	 */
	public function getCustomereCommerce()
	{
		return $this->customereCommerce;
	}
	
	/**
	 * @param boolean $isLocalTax
	 */
	public function setIsLocalTax($isLocalTax)
	{
		$this->isLocalTax = $isLocalTax;
	}
	
	/**
	 * @return boolean $isLocalTax
	 */
	public function getIsLocalTax()
	{
		return $this->isLocalTax;
	}
	
	/**
	 * @param string $localTaxName
	 */
	public function setLocalTaxName($localTaxName)
	{
		$this->localTaxName = $localTaxName;
	}
	
	/**
	 * @return string $localTaxName
	 */
	public function getLocalTaxName()
	{
		return $this->localTaxName;
	}
	
	/**
	 * @param float $localTaxtRate
	 */
	public function setLocalTaxtRate($localTaxtRate)
	{
		$this->localTaxtRate = $localTaxtRate;
	}
	
	/**
	 * @return float $localTaxtRate
	 */
	public function getLocalTaxtRate()
	{
		return $this->localTaxtRate;
	}
	
	/**
	 * @param long $holReport
	 */
	public function setHolReport($holReport)
	{
		$this->holReport = $holReport;
	}
	
	/**
	 * @return long $holReport
	 */
	public function getHolReport()
	{
		return $this->holReport;
	}
	
}
		
