<?php

/**
 * @package plugins.fairplay
 * @subpackage api.objects
 */
class KalturaFairplayEntryContextPluginData extends KalturaPluginData{

	/**
	 * For fairplay (and maybe in the future other drm providers) we need to return a public certificate to encrypt
	 * the request from the player to the server.
	 * @var string
	 */
	public $publicCertificate;

}