<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPlayingSource extends KalturaObject{

	/**
	 * @var string
	 */
	public $deliveryProfileId;
    
	/**
	 * @var string
	 */
	public $format;

	/**
	 * @var string
	 */
	public $priority;

	/**
	 * @var KalturaStringArray
	 */
	public $protocols;

	/**
	 * @var KalturaStringArray
	 */
	public $flavors;

	/**
	 * @var string
	 */
	public $url;

	/**
	 * @var KalturaPluginDataArray
	 */
	public $drm;

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

}