<?php
/**
 * @package plugins.youtubeApiDistribution
 * @subpackage api.objects
 *
 */
class KalturaYouTubeApiCaptionDistributionInfo extends KalturaObject{

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
	public $encryptionKey;
	
	/**
	 * @var string
	 */
	public $remoteId;
	
	/**
	 * @var KalturaYouTubeApiDistributionCaptionAction
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