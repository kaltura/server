<?php
class kHuluDistributionJobProviderData extends kDistributionJobProviderData
{
	/**
	 * @var string
	 */
	private $xml;
	
	/**
	 * @var string
	 */
	private $xmlFileName;
	
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
	private $aspectRatio;
	
	/**
	 * @var int
	 */
	private $frameRate;
	
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
	 * @return the $distributionProfileId
	 */
	public function getDistributionProfileId()
	{
		return $this->distributionProfileId;
	}

	/**
	 * @return the $aspectRatio
	 */
	public function getAspectRatio()
	{
		return $this->aspectRatio;
	}

	/**
	 * @return the $frameRate
	 */
	public function getFrameRate()
	{
		return $this->frameRate;
	}

	/**
	 * @param string $xml
	 */
	public function setXml($xml)
	{
		$this->xml = $xml;
	}

	/**
	 * @param int $metadataProfileId
	 */
	public function setMetadataProfileId($metadataProfileId)
	{
		$this->metadataProfileId = $metadataProfileId;
	}

	/**
	 * @param int $distributionProfileId
	 */
	public function setDistributionProfileId($distributionProfileId)
	{
		$this->distributionProfileId = $distributionProfileId;
	}

	/**
	 * @param string $aspectRatio
	 */
	public function setAspectRatio($aspectRatio)
	{
		$this->aspectRatio = $aspectRatio;
	}

	/**
	 * @param int $frameRate
	 */
	public function setFrameRate($frameRate)
	{
		$this->frameRate = $frameRate;
	}
	
	/**
	 * @return the $xmlFileName
	 */
	public function getXmlFileName()
	{
		return $this->xmlFileName;
	}

	/**
	 * @param string $xmlFileName
	 */
	public function setXmlFileName($xmlFileName)
	{
		$this->xmlFileName = $xmlFileName;
	}
}
