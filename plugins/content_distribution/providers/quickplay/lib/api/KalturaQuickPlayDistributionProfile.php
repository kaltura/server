<?php
/**
 * @package plugins.quickPlayDistribution
 * @subpackage api.objects
 */
class KalturaQuickPlayDistributionProfile extends KalturaConfigurableDistributionProfile
{
	/**
	 * @var string
	 */
	 public $sftpHost;
	 
	/**
	 * @var string
	 */
	 public $sftpLogin;
	 
	/**
	 * @var string
	 */
	 public $sftpPass;

	/**
	 * @var string
	 */
	public $sftpBasePath;
	 
	/**
	 * @var string
	 */
	 public $channelTitle;
	 
	/**
	 * @var string
	 */
	 public $channelLink;
	 
	/**
	 * @var string
	 */
	 public $channelDescription;
	 
	/**
	 * @var string
	 */
	 public $channelManagingEditor;
	 
	/**
	 * @var string
	 */
	 public $channelLanguage;
	 
	/**
	 * @var string
	 */
	 public $channelImageTitle;
	 
	/**
	 * @var string
	 */
	 public $channelImageWidth;
	 
	/**
	 * @var string
	 */
	 public $channelImageHeight;
	 
	/**
	 * @var string
	 */
	 public $channelImageLink;
	 
	/**
	 * @var string
	 */
	 public $channelImageUrl;
	 
	/**
	 * @var string
	 */
	 public $channelCopyright;
	 
	/**
	 * @var string
	 */
	 public $channelGenerator;
	 
	/**
	 * @var string
	 */
	 public $channelRating;
	 
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'sftpHost',
		'sftpLogin',
		'sftpPass',
		'sftpBasePath',
		'channelTitle',
		'channelLink',
		'channelDescription',
		'channelManagingEditor',
		'channelLanguage',
		'channelImageTitle',
		'channelImageWidth',
		'channelImageHeight',
		'channelImageLink',
		'channelImageUrl',
		'channelCopyright',
		'channelGenerator',
		'channelRating',
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
}