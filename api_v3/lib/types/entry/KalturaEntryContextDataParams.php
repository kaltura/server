<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaEntryContextDataParams extends KalturaAccessControlScope
{
	/**
	 * @var string
	 */
	public $flavorAssetId;
	
	/**
	 * @var string
	 */
	public $streamerType;
	
	/**
	 * @var string
	 */
	public $mediaProtocol;
}