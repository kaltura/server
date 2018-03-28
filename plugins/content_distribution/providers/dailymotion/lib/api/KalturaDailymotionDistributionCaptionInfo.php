<?php
/**
 * @package plugins.dailymotionDistribution
 * @subpackage api.objects
 *
 */
class KalturaDailymotionDistributionCaptionInfo extends KalturaObject{

	/**
	 * @var string
	 */
	public $language; 

	/**
	 * @var string
	 */
	public $remoteId;
	
	/**
	 * @var KalturaDailymotionDistributionCaptionAction
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
	 * @var KalturaDailymotionDistributionCaptionFormat
	 */
	public $format;
		
}