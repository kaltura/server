<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kPlaybackSource {

	/**
	 * @var string
	 */
	protected $deliveryProfileId;

	/**
	 * @var string
	 */
	protected $format;

	/**
	 * @var string
	 */
	protected $protocols;

	/**
	 * @var string
	 */
	protected $flavorIds;

	/**
	 * @var string
	 */
	protected $url;

	/**
	 * @var array
	 */
	protected $drm;

	public function __construct($deliveryProfileId = null, $format  = null, $protocols = null , $flavorIds = null, $url = null, $drm = null )
	{
		$this->deliveryProfileId = $deliveryProfileId;
		if ( $format == PlaybackProtocol::HTTP)
			$this->format = PlaybackProtocol::URL;
		else
			$this->format = $format;

		$this->protocols = $protocols;
		$this->flavorIds = $flavorIds;
		$this->url = $url;
		$this->drm = $drm;
	}

	/**
	 * @return string
	 */
	public function getDeliveryProfileId()
	{
		return $this->deliveryProfileId;
	}

	/**
	 * @param string $deliveryProfileId
	 */
	public function setDeliveryProfileId($deliveryProfileId)
	{
		$this->deliveryProfileId = $deliveryProfileId;
	}

	/**
	 * @return string
	 */
	public function getFormat()
	{
		return $this->format;
	}

	/**
	 * @param string $format
	 */
	public function setFormat($format)
	{
		$this->format = $format;
	}

	/**
	 * @return array
	 */
	public function getFlavorIds()
	{
		return $this->flavorIds;
	}

	/**
	 * @param array $flavorIds
	 */
	public function setFlavorIds($flavorIds)
	{
		$this->flavorIds = $flavorIds;
	}

	/**
	 * @return array
	 */
	public function getProtocols()
	{
		return $this->protocols;
	}

	/**
	 * @param array $protocols
	 */
	public function setProtocols($protocols)
	{
		$this->protocols = $protocols;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url)
	{
		$this->url = $url;
	}

	/**
	 * @return array
	 */
	public function getDrm()
	{
		return $this->drm;
	}

	/**
	 * @param array $drm
	 */
	public function setDrm($drm)
	{
		$this->drm = $drm;
	}
}