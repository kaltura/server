<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaLiveAsset extends KalturaFlavorAsset 
{
	/**
	 * @var string
	 * @requiresPermission all
	 */
	public $multicastIP;
	
	/**
	 * @var int
	 * @requiresPermission all
	 */
	public $multicastPort;
}
