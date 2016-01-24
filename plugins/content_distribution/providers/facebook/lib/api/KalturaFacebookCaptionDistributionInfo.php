<?php
/**
 * @package plugins.facebookDistribution
 * @subpackage api.objects
 *
 */
class KalturaFacebookCaptionDistributionInfo extends KalturaObject{

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
	 * @var KalturaFacebookDistributionCaptionAction
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