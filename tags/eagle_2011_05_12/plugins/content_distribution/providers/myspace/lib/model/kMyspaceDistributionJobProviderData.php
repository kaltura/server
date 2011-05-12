<?php
/**
 * @package plugins.myspaceDistribution
 * @subpackage model.data
 */
class kMyspaceDistributionJobProviderData extends kDistributionJobProviderData
{
	/**
	 * @var string
	 */
	private $xml;
		
	/**
	 * @var int
	 */
	private $metadataProfileId;
	
	/**
	 * @var int
	 */
	private $distributionProfileId;

	/**
	 * @var string
	 */
	private $myspFlavorAssetId;
	
	/**
	 * @var string
	 */
	private $feedTitle;

	/**
	 * @var string
	 */
	private $feedDescription;

	/**
	 * @var string
	 */
	private $feedContact;
	
	public function __construct(kDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
	}

	/**
	 * @return the $metadataProfileId
	 */
	public function getMetadataProfileId()
	{
		return $this->metadataProfileId;
	}
	
	/**
	 * @return the $distributionProfileId
	 */
	public function getDistributionProfileId()
	{
		return $this->distributionProfileId;
	}

	/**
	 * @param int $distributionProfileId
	 */
	public function setDistributionProfileId($distributionProfileId)
	{
		$this->distributionProfileId = $distributionProfileId;
	}
	
	/**
	 * @return the $xml
	 */
	public function getXml()
	{
		return $this->xml;
	}


	/**
	 * @param int $metadataProfileId
	 */
	public function setMetadataProfileId($metadataProfileId)
	{
		$this->metadataProfileId = $metadataProfileId;
	}


	/**
	 * @param string $xml
	 */
	public function setXml($xml)
	{
		$this->xml = $xml;
	}
	
	/**
	 * @param string $myspFlavorAssetId
	 */
	public function setMysplavorAssetId($myspFlavorAssetId)
	{
		$this->myspFlavorAssetId = $myspFlavorAssetId;
	}

	/**
	 * @return the $myspFlavorAssetId
	 */
	public function getMyspFlavorAssetId()
	{
		return $this->myspFlavorAssetId;
	}

	/**
	 * @param string $feedTitle
	 */
	public function setFeedTitle($feedTitle)
	{
		$this->feedTitle = $feedTitle;
	}

	/**
	 * @return the $feedTitle
	 */
	public function getFeedTitle()
	{
		return $this->feedTitle;
	}

	/**
	 * @param string $feedDescription
	 */
	public function setFeedDescription($feedDescription)
	{
		$this->feedDescription = $feedDescription;
	}

	/**
	 * @return the $feedDescription
	 */
	public function getFeedDescription()
	{
		return $this->feedDescription;
	}

	/**
	 * @param string $feedContact
	 */
	public function setFeedContact($feedContact)
	{
		$this->feedContact = $feedContact;
	}

	/**
	 * @return the $feedContact
	 */
	public function getFeedContact()
	{
		return $this->feedContact;
	}
	
}