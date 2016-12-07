<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kPlaybackSource {

	/**
	 * @var string
	 */
	private $deliveryProfileId;

	/**
	 * @var string
	 */
	private $format;

	/**
	 * @var string
	 */
	private $priority;

	/**
	 * @var array
	 */
	private $protocols;

	/**
	 * @var array
	 */
	private $flavors;

	/**
	 * @var string
	 */
	private $url;

	/**
	 * @var array
	 */
	private $drm;

	public function __construct($deliveryProfileId = null, $format  = null, $priority  = null, $protocols = null , $flavors = null, $url = null, $drm = null )
	{
		$this->deliveryProfileId = $deliveryProfileId;
		$this->format = $format;
		$this->priority = $priority;
		$this->protocols = $protocols;
		$this->flavors = $flavors;
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
	 * @return string
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * @param string $priority
	 */
	public function setPriority($priority)
	{
		$this->priority = $priority;
	}

	/**
	 * @return array
	 */
	public function getFlavors()
	{
		return $this->flavors;
	}

	/**
	 * @param array $flavors
	 */
	public function setFlavors($flavors)
	{
		$this->flavors = $flavors;
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