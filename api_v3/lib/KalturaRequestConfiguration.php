<?php

/**
 * Define client request optional configurations
 */
class KalturaRequestConfiguration
{
	/**
	 * Impersonated partner id
	 * @var int
	 */
	public $partnerId;
	
	/**
	 * Kaltura API session
	 * @alias session
	 * @var string
	 */
	public $ks;
	
	/**
	 * Response profile
	 * @var KalturaNestedResponseProfileBase
	 */
	public $responseProfile;
}