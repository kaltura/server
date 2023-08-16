<?php

/**
 * @package plugins.vendor
 * @subpackage api.objects
 */

class KalturaEndpointValidationResponse extends KalturaObject
{
	/**
	 * @var string
	 */
	public $plainToken;

	/**
	 * @var string
	 */
	public $encryptedToken;
}