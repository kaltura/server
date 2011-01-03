<?php
class kComcastDistributionJobProviderData extends kDistributionJobProviderData
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
	 * @var string
	 */
	private $thumbAssetId;
	
	/**
	 * @var string
	 */
	private $flavorAssetId;
	
	/**
	 * @var string
	 */
	private $keywords;
	
	/**
	 * @var string
	 */
	private $author;
	
	/**
	 * @var string
	 */
	private $album;
	
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
	 * @return the $thumbAssetId
	 */
	public function getThumbAssetId()
	{
		return $this->thumbAssetId;
	}

	/**
	 * @return the $flavorAssetId
	 */
	public function getFlavorAssetId()
	{
		return $this->flavorAssetId;
	}

	/**
	 * @return the $keywords
	 */
	public function getKeywords()
	{
		return $this->keywords;
	}

	/**
	 * @return the $author
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * @return the $album
	 */
	public function getAlbum()
	{
		return $this->album;
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
	 * @param string $thumbAssetId
	 */
	public function setThumbAssetId($thumbAssetId)
	{
		$this->thumbAssetId = $thumbAssetId;
	}

	/**
	 * @param string $flavorAssetId
	 */
	public function setFlavorAssetId($flavorAssetId)
	{
		$this->flavorAssetId = $flavorAssetId;
	}

	/**
	 * @param string $keywords
	 */
	public function setKeywords($keywords)
	{
		$this->keywords = $keywords;
	}

	/**
	 * @param string $author
	 */
	public function setAuthor($author)
	{
		$this->author = $author;
	}

	/**
	 * @param string $album
	 */
	public function setAlbum($album)
	{
		$this->album = $album;
	}
}