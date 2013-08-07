<?php
require_once(__DIR__ . '/WebexXmlRequestType.class.php');

class WebexXmlSiteDisplayTypeType extends WebexXmlRequestType
{
	/**
	 *
	 * @var boolean
	 */
	protected $prodSvcAnnounce;
	
	/**
	 *
	 * @var boolean
	 */
	protected $trainingInfo;
	
	/**
	 *
	 * @var boolean
	 */
	protected $eNewsletters;
	
	/**
	 *
	 * @var boolean
	 */
	protected $promotionsOffers;
	
	/**
	 *
	 * @var boolean
	 */
	protected $pressReleases;
	
	/* (non-PHPdoc)
	 * @see WebexXmlObject::getAttributeType()
	 */
	protected function getAttributeType($attributeName)
	{
		switch ($attributeName)
		{
			case 'prodSvcAnnounce':
				return 'boolean';
	
			case 'trainingInfo':
				return 'boolean';
	
			case 'eNewsletters':
				return 'boolean';
	
			case 'promotionsOffers':
				return 'boolean';
	
			case 'pressReleases':
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
			'prodSvcAnnounce',
			'trainingInfo',
			'eNewsletters',
			'promotionsOffers',
			'pressReleases',
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
		return 'displayTypeType';
	}
	
	/**
	 * @param boolean $prodSvcAnnounce
	 */
	public function setProdSvcAnnounce($prodSvcAnnounce)
	{
		$this->prodSvcAnnounce = $prodSvcAnnounce;
	}
	
	/**
	 * @return boolean $prodSvcAnnounce
	 */
	public function getProdSvcAnnounce()
	{
		return $this->prodSvcAnnounce;
	}
	
	/**
	 * @param boolean $trainingInfo
	 */
	public function setTrainingInfo($trainingInfo)
	{
		$this->trainingInfo = $trainingInfo;
	}
	
	/**
	 * @return boolean $trainingInfo
	 */
	public function getTrainingInfo()
	{
		return $this->trainingInfo;
	}
	
	/**
	 * @param boolean $eNewsletters
	 */
	public function setENewsletters($eNewsletters)
	{
		$this->eNewsletters = $eNewsletters;
	}
	
	/**
	 * @return boolean $eNewsletters
	 */
	public function getENewsletters()
	{
		return $this->eNewsletters;
	}
	
	/**
	 * @param boolean $promotionsOffers
	 */
	public function setPromotionsOffers($promotionsOffers)
	{
		$this->promotionsOffers = $promotionsOffers;
	}
	
	/**
	 * @return boolean $promotionsOffers
	 */
	public function getPromotionsOffers()
	{
		return $this->promotionsOffers;
	}
	
	/**
	 * @param boolean $pressReleases
	 */
	public function setPressReleases($pressReleases)
	{
		$this->pressReleases = $pressReleases;
	}
	
	/**
	 * @return boolean $pressReleases
	 */
	public function getPressReleases()
	{
		return $this->pressReleases;
	}
	
}
		
