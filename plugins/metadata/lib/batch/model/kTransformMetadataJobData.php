<?php
/**
 * @package plugins.metadata
 * @subpackage model.data
 */
class kTransformMetadataJobData extends kJobData
{
	/**
	 * @var FileContainer
	 */
	private $srcXsl;
	
	/**
	 * @var int
	 */
	private $srcVersion;
	
	/**
	 * @var int
	 */
	private $destVersion;
	
	/**
	 * @var string
	 */
	private $destXsdPath;
	
	/**
	 * @var int
	 */
	private $metadataProfileId;
	
	
	/**
	 * @return FileContainer $srcXsl
	 */
	public function getSrcXsl()
	{
		return $this->srcXsl;
	}

	/**
	 * @return the $srcVersion
	 */
	public function getSrcVersion()
	{
		return $this->srcVersion;
	}

	/**
	 * @return the $destVersion
	 */
	public function getDestVersion()
	{
		return $this->destVersion;
	}

	/**
	 * @return the $metadataProfileId
	 */
	public function getMetadataProfileId()
	{
		return $this->metadataProfileId;
	}

	/**
	 * @param FileContainer $srcXsl
	 */
	public function setSrcXsl($srcXsl)
	{
		$this->srcXsl = $srcXsl;
	}

	/**
	 * @param $srcVersion the $srcVersion to set
	 */
	public function setSrcVersion($srcVersion)
	{
		$this->srcVersion = $srcVersion;
	}

	/**
	 * @param $destVersion the $destVersion to set
	 */
	public function setDestVersion($destVersion)
	{
		$this->destVersion = $destVersion;
	}

	/**
	 * @param $metadataProfileId the $metadataProfileId to set
	 */
	public function setMetadataProfileId($metadataProfileId)
	{
		$this->metadataProfileId = $metadataProfileId;
	}
	
	/**
	 * @return the $destXsdPath
	 */
	public function getDestXsdPath()
	{
		return $this->destXsdPath;
	}

	/**
	 * @param $destXsdPath the $destXsdPath to set
	 */
	public function setDestXsdPath($destXsdPath)
	{
		$this->destXsdPath = $destXsdPath;
	}
}
