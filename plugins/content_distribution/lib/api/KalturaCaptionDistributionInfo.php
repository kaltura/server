<?php
/**
 * @package plugins.contentDistribution
 * @subpackage api.objects
 *
 */
class KalturaCaptionDistributionInfo extends KalturaObject{

	/**
	 * @var string
	 */
	public $language; 
	
	/**
	 * @var string
	 */
	public $label; 
	
	/**
	 * @var string
	 */
	public $filePath;
	
	/**
	 * @var string
	 */
	public $remoteId;
	
	/**
	 * @var KalturaDistributionAction
	 */
	public $action;	
	
	/**
	 * @var string
	 */
	public $version;
	
	/**
	 * @var string
	 */
	public $assetId;
		
}