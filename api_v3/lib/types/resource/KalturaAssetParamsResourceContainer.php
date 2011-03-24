<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAssetParamsResourceContainer extends KalturaResource 
{
	/**
	 * The content resource to associate with asset params
	 * @var KalturaContentResource
	 */
	public $resource;
	
	/**
	 * The asset params to associate with the reaource
	 * @var string
	 */
	public $assetParamsId;
}