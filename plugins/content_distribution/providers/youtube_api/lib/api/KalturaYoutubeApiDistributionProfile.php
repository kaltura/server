<?php
/**
 * @package plugins.youtubeApiDistribution
 * @subpackage api.objects
 */
class KalturaYoutubeApiDistributionProfile extends KalturaConfigurableDistributionProfile
{
	/**
	 * @var string
	 */
	public $username;

	/**
	 * 
	 * @var int
	 */
	public $defaultCategory;
		
	/**
	 * 
	 * @var string
	 */
	public $allowComments;
	
	/**
	 * 
	 * @var string
	 */
	public $allowEmbedding;
	
	/**
	 * 
	 * @var string
	 */
	public $allowRatings;
	
	/**
	 * 
	 * @var string
	 */
	public $allowResponses;
	
	/**
	 * @var string
	 */
	public $apiAuthorizeUrl;

	/**
	 * @var string
	 */
	public $googleClientId;

	/**
	 * @var string
	 */
	public $googleClientSecret;

	/**
	 * @var string
	 */
	public $googleTokenData;

	/**
	 * @var bool
	 */
	public $assumeSuccess;

	/**
	 * @var string
	 */
	public $privacyStatus;
	
	/*
	 * mapping between the field on this object (on the left) and the setter/getter on the object (on the right)  
	 */
	private static $map_between_objects = array 
	(
		'username',
		'defaultCategory',
		'allowComments',
		'allowEmbedding',
		'allowRatings',
		'allowResponses',
		'apiAuthorizeUrl',
		'assumeSuccess',
		'privacyStatus',
	 );
		 
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::doFromObject($srcObj, $responseProfile)
	 */
	protected function doFromObject($distributionProfile, KalturaDetachedResponseProfile $responseProfile = null)
	{
		/* @var $distributionProfile YoutubeApiDistributionProfile */
		parent::doFromObject($distributionProfile, $responseProfile);
		
		$appId = YoutubeApiDistributionPlugin::GOOGLE_APP_ID;
		$authConfig = kConf::get($appId, 'google_auth', null);
		
		$this->googleClientId = isset($authConfig['clientId']) ? $authConfig['clientId'] : null;
		$this->googleClientSecret = isset($authConfig['clientSecret']) ? $authConfig['clientSecret'] : null;

		$tokenData = $distributionProfile->getGoogleOAuth2Data();
		if ($tokenData)
		{
			$this->googleTokenData = json_encode($tokenData);
		}
	}
}