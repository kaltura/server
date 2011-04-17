<?php
/**
 * @package plugins.verizonDistribution
 * @subpackage model.data
 */
class kVerizonDistributionJobProviderData extends kDistributionJobProviderData
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
	private $vrzFlavorAssetId;
	
	/**
	 * @var string
	 */
	private $providerName;
	/**
	 * @var string
	 */
	private $providerId;
	
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
	 * @param string $vrzFlavorAssetId
	 */
	public function setVrzlavorAssetId($vrzFlavorAssetId)
	{
		$this->vrzFlavorAssetId = $vrzFlavorAssetId;
	}

	/**
	 * @return the $vrzFlavorAssetId
	 */
	public function getVrzFlavorAssetId()
	{
		return $this->vrzFlavorAssetId;
	}

	/**
	 * @param string $providerName
	 */
	public function setProviderName($providerName)
	{
		$this->providerName = $providerName;
	}

	/**
	 * @return the $providerName
	 */
	public function getProviderName()
	{
		return $this->providerName;
	}

	/**
	 * @param string $providerId
	 */
	public function setProviderId($providerId)
	{
		$this->providerId = $providerId;
	}

	/**
	 * @return the $providerId
	 */
	public function getProviderId()
	{
		return $this->providerId;
	}
	
}