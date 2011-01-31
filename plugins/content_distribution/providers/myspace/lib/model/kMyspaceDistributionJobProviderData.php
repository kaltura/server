<?php
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
	 * @return the $metadataProfileId
	 */
	public function getMetadataProfileId()
	{
		return $this->metadataProfileId;
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
}