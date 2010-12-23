<?php
class kMsnDistributionJobProviderData extends kDistributionJobProviderData
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
	 * @var string
	 */
	private $movFlavorAssetId;
	
	/**
	 * @var string
	 */
	private $flvFlavorAssetId;
	
	/**
	 * @var string
	 */
	private $wmvFlavorAssetId;
	
	/**
	 * @var string
	 */
	private $thumbAssetId;
	
	/**
	 * @var int
	 */
	private $emailed;
	
	/**
	 * @var int
	 */
	private $rated;
	
	/**
	 * @var int
	 */
	private $blogged;
	
	/**
	 * @var int
	 */
	private $reviewed;
	
	/**
	 * @var int
	 */
	private $bookmarked;
	
	/**
	 * @var int
	 */
	private $playbackFailed;
	
	/**
	 * @var int
	 */
	private $timeSpent;
	
	/**
	 * @var int
	 */
	private $recommended;

	/**
	 * @return the $emailed
	 */
	public function getEmailed()
	{
		return $this->emailed;
	}

	/**
	 * @return the $rated
	 */
	public function getRated()
	{
		return $this->rated;
	}

	public function __construct(kDistributionJobData $distributionJobData = null)
	{
		parent::__construct($distributionJobData);
	}

	/**
	 * @return the $blogged
	 */
	public function getBlogged()
	{
		return $this->blogged;
	}

	/**
	 * @return the $reviewed
	 */
	public function getReviewed()
	{
		return $this->reviewed;
	}

	/**
	 * @return the $bookmarked
	 */
	public function getBookmarked()
	{
		return $this->bookmarked;
	}

	/**
	 * @return the $playbackFailed
	 */
	public function getPlaybackFailed()
	{
		return $this->playbackFailed;
	}

	/**
	 * @return the $timeSpent
	 */
	public function getTimeSpent()
	{
		return $this->timeSpent;
	}

	/**
	 * @return the $recommended
	 */
	public function getRecommended()
	{
		return $this->recommended;
	}

	/**
	 * @param int $emailed
	 */
	public function setEmailed($emailed)
	{
		$this->emailed = $emailed;
	}

	/**
	 * @param int $rated
	 */
	public function setRated($rated)
	{
		$this->rated = $rated;
	}

	/**
	 * @param int $blogged
	 */
	public function setBlogged($blogged)
	{
		$this->blogged = $blogged;
	}

	/**
	 * @param int $reviewed
	 */
	public function setReviewed($reviewed)
	{
		$this->reviewed = $reviewed;
	}

	/**
	 * @param int $bookmarked
	 */
	public function setBookmarked($bookmarked)
	{
		$this->bookmarked = $bookmarked;
	}

	/**
	 * @param int $playbackFailed
	 */
	public function setPlaybackFailed($playbackFailed)
	{
		$this->playbackFailed = $playbackFailed;
	}

	/**
	 * @param int $timeSpent
	 */
	public function setTimeSpent($timeSpent)
	{
		$this->timeSpent = $timeSpent;
	}

	/**
	 * @param int $recommended
	 */
	public function setRecommended($recommended)
	{
		$this->recommended = $recommended;
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