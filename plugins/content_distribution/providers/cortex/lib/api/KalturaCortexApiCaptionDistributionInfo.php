<?php
/**
 * @package plugins.cortexApiDistribution
 * @subpackage api.objects
 *
 */
class KalturaCortexApiCaptionDistributionInfo extends KalturaObject{

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
	 * @var KalturaCortexApiDistributionCaptionAction
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

	/**
	 * @var string
	 */
	public $fileExt;
		
}
