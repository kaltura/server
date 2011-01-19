<?php
class kVerizonDistributionJobProviderData extends kDistributionJobProviderData
{
	/**
	 * @var string
	 */
	private $xml;
	
	/**
	 * @var string
	 */
	private $csId;
	
	/**
	 * @var string
	 */
	private $source;
	
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
	 * @return the $csId
	 */
	public function getCsId()
	{
		return $this->csId;
	}

	/**
	 * @return the $source
	 */
	public function getSource()
	{
		return $this->source;
	}

	/**
	 * @return the $metadataProfileId
	 */
	public function getMetadataProfileId()
	{
		return $this->metadataProfileId;
	}

	/**
	 * @return the $movFlavorAssetId
	 */
	public function getMovFlavorAssetId()
	{
		return $this->movFlavorAssetId;
	}

	/**
	 * @return the $flvFlavorAssetId
	 */
	public function getFlvFlavorAssetId()
	{
		return $this->flvFlavorAssetId;
	}

	/**
	 * @return the $wmvFlavorAssetId
	 */
	public function getWmvFlavorAssetId()
	{
		return $this->wmvFlavorAssetId;
	}

	/**
	 * @return the $thumbAssetId
	 */
	public function getThumbAssetId()
	{
		return $this->thumbAssetId;
	}

	/**
	 * @param string $csId
	 */
	public function setCsId($csId)
	{
		$this->csId = $csId;
	}

	/**
	 * @param string $source
	 */
	public function setSource($source)
	{
		$this->source = $source;
	}

	/**
	 * @param int $metadataProfileId
	 */
	public function setMetadataProfileId($metadataProfileId)
	{
		$this->metadataProfileId = $metadataProfileId;
	}

	/**
	 * @param string $movFlavorAssetId
	 */
	public function setMovFlavorAssetId($movFlavorAssetId)
	{
		$this->movFlavorAssetId = $movFlavorAssetId;
	}

	/**
	 * @param string $flvFlavorAssetId
	 */
	public function setFlvFlavorAssetId($flvFlavorAssetId)
	{
		$this->flvFlavorAssetId = $flvFlavorAssetId;
	}

	/**
	 * @param string $wmvFlavorAssetId
	 */
	public function setWmvFlavorAssetId($wmvFlavorAssetId)
	{
		$this->wmvFlavorAssetId = $wmvFlavorAssetId;
	}

	/**
	 * @param string $thumbAssetId
	 */
	public function setThumbAssetId($thumbAssetId)
	{
		$this->thumbAssetId = $thumbAssetId;
	}

	/**
	 * @param string $xml
	 */
	public function setXml($xml)
	{
		$this->xml = $xml;
	}
}