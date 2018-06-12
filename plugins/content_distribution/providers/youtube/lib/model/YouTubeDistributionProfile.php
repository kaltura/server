<?php
/**
 * @package plugins.youTubeDistribution
 * @subpackage model
 */
class YouTubeDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_FEED_SPEC_VERSION = 'apiVersion';
	const CUSTOM_DATA_USERNAME = 'username';
	const CUSTOM_DATA_OWNER_NAME = 'ownerName';
	const CUSTOM_DATA_NOTIFICATION_EMAIL = 'notificationEmail';
	const CUSTOM_DATA_SFTP_HOST = 'sftpHost';
	const CUSTOM_DATA_SFTP_PORT = 'sftpPort';
	const CUSTOM_DATA_SFTP_LOGIN = 'sftpLogin';
	const CUSTOM_DATA_SFTP_PUBLIC_KEY = 'sftpPublicKey';
	const CUSTOM_DATA_SFTP_PRIVATE_KEY = 'sftpPrivateKey';
	const CUSTOM_DATA_SFTP_BASE_DIRECTORY = 'sftpBaseDir';
	const CUSTOM_DATA_DEFAULT_CATEGORY = 'defaultCategory';
	const CUSTOM_DATA_ALLOW_COMMENTS = 'allowComments';
	const CUSTOM_DATA_ALLOW_EMBEDDING = 'allowEmbedding';
	const CUSTOM_DATA_ALLOW_RATINGS = 'allowRatings';
	const CUSTOM_DATA_ALLOW_RESPONSES = 'allowResponses';
	const CUSTOM_DATA_COMMENRCIAL_POLICY = 'commercialPolicy';
	const CUSTOM_DATA_UGC_POLICY = 'ugcPolicy';
	const CUSTOM_DATA_TARGET = 'target';
	const CUSTOM_DATA_AD_SERVER_PARTNER_ID = 'adServerPartnerId';
	const CUSTOM_DATA_ENABLE_AD_SERVER = 'enableAdServer';
	const CUSTOM_DATA_ALLOW_PRE_ROLL_ADS = 'allowPreRollAds';
	const CUSTOM_DATA_ALLOW_POST_ROLL_ADS = 'allowPostRollAds';
	const CUSTOM_DATA_STRICT = 'strict';
	const CUSTOM_DATA_OVERRIDE_MANUAL_EDITS = 'overrideManualEdits';
	const CUSTOM_DATA_URGENT_REFERENCE = 'urgentReference';
	const CUSTOM_DATA_ALLOW_SYNDICATION = 'allowSyndication';
	const CUSTOM_DATA_HIDE_VIEW_COUNT = 'hideViewCount';
	const CUSTOM_DATA_ALLOW_ADSENSE_FOR_VIDEO = 'allowAdsenseForVideo';
	const CUSTOM_DATA_ALLOW_INVIDEO = 'allowInvideo';
	const CUSTOM_DATA_ALLOW_MID_ROLL_ADS = 'allowMidRollAds';
	const CUSTOM_DATA_INSTREAM_STANDARD = 'instreamStandard';
	const CUSTOM_DATA_INSTREAM_TRUEVIEW = 'instreamTrueview';
	const CUSTOM_DATA_THIRD_PARTY_ADS = 'thirdPartyAds';
	const CUSTOM_DATA_PRODUCT_LISTING_ADS = 'productListingAds';
	const CUSTOM_DATA_CLAIM_TYPE = 'claimType';
	const CUSTOM_DATA_BLOCK_OUTSIDE_OWNERSHIP = 'blockOutsideOwnership';
	const CUSTOM_DATA_CAPTION_AUTOSYNC = 'captionAutosync';
	const CUSTOM_DATA_DELETE_REFERENCE = 'deleteReference';
	const CUSTOM_DATA_RELEASE_CLAIMS = 'releaseClaims';
	const CUSTOM_DATA_PRIVACY_STATUS = 'privacyStatus';
	const CUSTOM_DATA_ENABLE_CONTENT_ID = 'enableContentId';
	const CUSTOM_DATA_DOMAIN_WHITELIST = 'domainWhitlist';
	const CUSTOM_DATA_NOTIFY_SUBSCRIBERS = "notifySubscribers";

	// validations
	const MEDIA_TITLE_MAXIMUM_LENGTH = 100;
	const MEDIA_DESCRIPTION_MAXIMUM_LENGTH = 5000;
	const MEDIA_KEYWORDS_MAXIMUM_TOTAL_LENGTH = 500;
	const MEDIA_KEYWORDS_MINIMUM_LENGTH_EACH_KEYWORD = 2;
	const MEDIA_KEYWORDS_MAXIMUM_LENGTH_EACH_KEYWORD = 30;
	const METADATA_CUSTOM_ID_MAXIMUM_LENGTH = 64;
	const TV_METADATA_EPISODE_MAXIMUM_LENGTH = 16;
	const TV_METADATA_SEASON_MAXIMUM_LENGTH = 16;
	const TV_METADATA_EPISODE_TITLE_MAXIMUM_LENGTH = 64;
	const TV_METADATA_SHOW_TITLE_MAXIMUM_LENGTH = 64;
	const TV_METADATA_TMS_ID_MAXIMUM_LENGTH = 14;
	const MOVIE_METADATA_TITLE_MAXIMUM_LENGTH = 64;
	const MOVIE_METADATA_TMS_ID_MAXIMUM_LENGTH = 14;
	
	const MEDIA_RATING_VALID_VALUES = 'adult,nonadult';
	const ALLOW_COMMENTS_VALID_VALUES = 'Always,Approve,Never';
	const ALLOW_RESPONSES_VALID_VALUES = 'Always,Approve,Never';
	const ALLOW_EMBEDDING_VALID_VALUES = 'true,false';
	const ALLOW_RATINGS_VALID_VALUES = 'true,false';
	const ADVERTISING_INVIDEO_VALID_VALUES = 'Allow,Deny';
	const ADVERTISING_ADSENSE_FOR_VIDEO_VALUES = 'Allow,Deny';
	const DISTRIBUTION_RESTRICTION_DISTRIBUTION_RULE_VALUES = 'Allow,Deny';
	const URGENT_REFERENCE_FILE_VALUES = 'yes,no';
	const KEEP_FINGERPRINT_VALUES = 'yes,no';

	const METADATA_FIELD_PRIVACY_STATUS = 'YoutubePrivacyStatus';

	protected $specV1OnlyFields = array(
		YouTubeDistributionField::OWNER_NAME,
		YouTubeDistributionField::TARGET,
		YouTubeDistributionField::LANGUAGE,
		YouTubeDistributionField::KEEP_FINGERPRINT,
		YouTubeDistributionField::ACCOUNT_USERNAME,
		YouTubeDistributionField::ACCOUNT_PASSWORD,
		YouTubeDistributionField::WEB_METADATA_CUSTOM_ID,
		YouTubeDistributionField::WEB_METADATA_NOTES,
		YouTubeDistributionField::MOVIE_METADATA_CUSTOM_ID,
		YouTubeDistributionField::MOVIE_METADATA_DIRECTOR,
		YouTubeDistributionField::MOVIE_METADATA_TITLE,
		YouTubeDistributionField::MOVIE_METADATA_TMS_ID,
		YouTubeDistributionField::TV_METADATA_CUSTOM_ID,
		YouTubeDistributionField::TV_METADATA_SHOW_TITLE,
		YouTubeDistributionField::TV_METADATA_EPISODE,
		YouTubeDistributionField::TV_METADATA_EPISODE_TITLE,
		YouTubeDistributionField::TV_METADATA_NOTES,
		YouTubeDistributionField::TV_METADATA_SEASON,
		YouTubeDistributionField::TV_METADATA_TMS_ID,
		YouTubeDistributionField::ALLOW_EMBEDDING,
	);

	protected $specV2OnlyFields = array(

		YouTubeDistributionField::ADVERTISING_ALLOW_MID_ROLL_ADS,
		YouTubeDistributionField::ASSET_TYPE,
		YouTubeDistributionField::ASSET_OVERRIDE_MANUAL_EDITS,
		YouTubeDistributionField::ASSET_ACTOR,
		YouTubeDistributionField::ASSET_BROADCASTER,
		YouTubeDistributionField::ASSET_CONTENT_TYPE,
		YouTubeDistributionField::ASSET_CUSTOM_ID,
		YouTubeDistributionField::ASSET_DESCRIPTION,
		YouTubeDistributionField::ASSET_DIRECTOR,
		YouTubeDistributionField::ASSET_EIDR,
		YouTubeDistributionField::ASSET_END_YEAR,
		YouTubeDistributionField::ASSET_EPISODE,
		YouTubeDistributionField::ASSET_GENRE,
		YouTubeDistributionField::ASSET_GRID,
		YouTubeDistributionField::ASSET_ISAN,
		YouTubeDistributionField::ASSET_KEYWORDS,
		YouTubeDistributionField::ASSET_NOTES,
		YouTubeDistributionField::ASSET_ORIGINAL_RELEASE_DATE,
		YouTubeDistributionField::ASSET_ORIGINAL_RELEASE_MEDIUM,
		YouTubeDistributionField::ASSET_PRODUCER,
		YouTubeDistributionField::ASSET_RATING_SYSTEM,
		YouTubeDistributionField::ASSET_RATING_VALUE,
		YouTubeDistributionField::ASSET_SEASON,
		YouTubeDistributionField::ASSET_SHOW_AND_MOVIE_PROGRAMMING,
		YouTubeDistributionField::ASSET_SHOW_TITLE,
		YouTubeDistributionField::ASSET_SPOKEN_LANGUAGE,
		YouTubeDistributionField::ASSET_START_YEAR,
		YouTubeDistributionField::ASSET_SUBTITLED_LANGUAGE,
		YouTubeDistributionField::ASSET_TITLE,
		YouTubeDistributionField::ASSET_TMS_ID,
		YouTubeDistributionField::ASSET_UPC,
		YouTubeDistributionField::ASSET_URL,
		YouTubeDistributionField::ASSET_WRITER,
		YouTubeDistributionField::VIDEO_ALLOW_COMMENT_RATINGS,
		YouTubeDistributionField::VIDEO_ALLOW_SYNDICATION,
		YouTubeDistributionField::VIDEO_CHANNEL,
		YouTubeDistributionField::VIDEO_HIDE_VIEW_COUNT,
		YouTubeDistributionField::VIDEO_DOMAIN_BLACK_LIST,
		YouTubeDistributionField::VIDEO_DOMAIN_WHITE_LIST,
		YouTubeDistributionField::VIDEO_NOTIFY_SUBSCRIBERS,
		YouTubeDistributionField::VIDEO_PUBLIC,
		YouTubeDistributionField::CLAIM_TYPE,
		YouTubeDistributionField::CLAIM_BLOCK_OUTSIDE_OWNERSHIP,
		YouTubeDistributionField::ADVERTISING_INSTREAM_STANDARD,
		YouTubeDistributionField::DISABLE_FINGERPRINTING,
		YouTubeDistributionField::ALLOW_EMBEDDING,
	);

	protected $specV3NonRelevantFields = array(

		YouTubeDistributionField::MOVIE_METADATA_NOTES,
		YouTubeDistributionField::DATE_RECORDED,
		YouTubeDistributionField::URGENT_REFERENCE_FILE,
		YouTubeDistributionField::ALLOW_COMMENTS,
		YouTubeDistributionField::ALLOW_RATINGS,
		YouTubeDistributionField::ALLOW_RESPONSES,
		YouTubeDistributionField::THIRD_PARTY_AD_SERVER_PARTNER_ID,
		YouTubeDistributionField::THIRD_PARTY_AD_SERVER_AD_TYPE,
		YouTubeDistributionField::ADVERTISING_ALLOW_PRE_ROLL_ADS,
		YouTubeDistributionField::ADVERTISING_ALLOW_POST_ROLL_ADS,
		YouTubeDistributionField::OWNER_NAME,
		YouTubeDistributionField::TARGET,
		YouTubeDistributionField::LANGUAGE,
		YouTubeDistributionField::KEEP_FINGERPRINT,
		YouTubeDistributionField::ACCOUNT_USERNAME,
		YouTubeDistributionField::ACCOUNT_PASSWORD,
		YouTubeDistributionField::WEB_METADATA_CUSTOM_ID,
		YouTubeDistributionField::WEB_METADATA_NOTES,
		YouTubeDistributionField::MOVIE_METADATA_CUSTOM_ID,
		YouTubeDistributionField::MOVIE_METADATA_DIRECTOR,
		YouTubeDistributionField::MOVIE_METADATA_TITLE,
		YouTubeDistributionField::MOVIE_METADATA_TMS_ID,
		YouTubeDistributionField::TV_METADATA_CUSTOM_ID,
		YouTubeDistributionField::TV_METADATA_SHOW_TITLE,
		YouTubeDistributionField::TV_METADATA_EPISODE,
		YouTubeDistributionField::TV_METADATA_EPISODE_TITLE,
		YouTubeDistributionField::TV_METADATA_NOTES,
		YouTubeDistributionField::TV_METADATA_SEASON,
		YouTubeDistributionField::TV_METADATA_TMS_ID,
		YouTubeDistributionField::ALLOW_EMBEDDING,
		YouTubeDistributionField::ADVERTISING_ALLOW_MID_ROLL_ADS,
		YouTubeDistributionField::ASSET_TYPE,
		YouTubeDistributionField::ASSET_OVERRIDE_MANUAL_EDITS,
		YouTubeDistributionField::ASSET_ACTOR,
		YouTubeDistributionField::ASSET_BROADCASTER,
		YouTubeDistributionField::ASSET_CONTENT_TYPE,
		YouTubeDistributionField::ASSET_DESCRIPTION,
		YouTubeDistributionField::ASSET_DIRECTOR,
		YouTubeDistributionField::ASSET_EIDR,
		YouTubeDistributionField::ASSET_END_YEAR,
		YouTubeDistributionField::ASSET_EPISODE,
		YouTubeDistributionField::ASSET_GENRE,
		YouTubeDistributionField::ASSET_GRID,
		YouTubeDistributionField::ASSET_ISAN,
		YouTubeDistributionField::ASSET_KEYWORDS,
		YouTubeDistributionField::ASSET_NOTES,
		YouTubeDistributionField::ASSET_ORIGINAL_RELEASE_DATE,
		YouTubeDistributionField::ASSET_ORIGINAL_RELEASE_MEDIUM,
		YouTubeDistributionField::ASSET_PRODUCER,
		YouTubeDistributionField::ASSET_RATING_SYSTEM,
		YouTubeDistributionField::ASSET_RATING_VALUE,
		YouTubeDistributionField::ASSET_SEASON,
		YouTubeDistributionField::ASSET_SHOW_AND_MOVIE_PROGRAMMING,
		YouTubeDistributionField::ASSET_SHOW_TITLE,
		YouTubeDistributionField::ASSET_START_YEAR,
		YouTubeDistributionField::ASSET_SUBTITLED_LANGUAGE,
		YouTubeDistributionField::ASSET_TMS_ID,
		YouTubeDistributionField::ASSET_UPC,
		YouTubeDistributionField::ASSET_URL,
		YouTubeDistributionField::ASSET_WRITER,
		YouTubeDistributionField::VIDEO_ALLOW_COMMENT_RATINGS,
		YouTubeDistributionField::VIDEO_ALLOW_SYNDICATION,
		YouTubeDistributionField::VIDEO_HIDE_VIEW_COUNT,
		YouTubeDistributionField::VIDEO_DOMAIN_BLACK_LIST,
		YouTubeDistributionField::VIDEO_PUBLIC,
		YouTubeDistributionField::CLAIM_TYPE,
		YouTubeDistributionField::DISABLE_FINGERPRINTING,
		YouTubeDistributionField::ALLOW_EMBEDDING,
		YouTubeDistributionField::MEDIA_RATING,
		YouTubeDistributionField::MEDIA_TITLE,
		YouTubeDistributionField::LOCATION_COUNTRY,
		YouTubeDistributionField::LOCATION_LOCATION_TEXT,
		YouTubeDistributionField::LOCATION_ZIP_CODE,
		YouTubeDistributionField::DISTRIBUTION_RESTRICTION_DISTRIBUTION_RULE,

	);

	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return YouTubeDistributionPlugin::getProvider();
	}
	
		
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
	    $validationErrors = parent::validateForSubmission($entryDistribution, $action);
		
		$maxLengthFields = array (
		    YouTubeDistributionField::MEDIA_DESCRIPTION => self::MEDIA_DESCRIPTION_MAXIMUM_LENGTH,
		    YouTubeDistributionField::MEDIA_TITLE => self::MEDIA_TITLE_MAXIMUM_LENGTH,
			YouTubeDistributionField::MEDIA_KEYWORDS => self::MEDIA_KEYWORDS_MAXIMUM_TOTAL_LENGTH,
		    YouTubeDistributionField::WEB_METADATA_CUSTOM_ID => self::METADATA_CUSTOM_ID_MAXIMUM_LENGTH,
		    YouTubeDistributionField::MOVIE_METADATA_CUSTOM_ID => self::METADATA_CUSTOM_ID_MAXIMUM_LENGTH,
		    YouTubeDistributionField::TV_METADATA_CUSTOM_ID => self::METADATA_CUSTOM_ID_MAXIMUM_LENGTH,
		    YouTubeDistributionField::TV_METADATA_EPISODE => self::TV_METADATA_EPISODE_MAXIMUM_LENGTH,
		    YouTubeDistributionField::TV_METADATA_EPISODE_TITLE => self::TV_METADATA_EPISODE_TITLE_MAXIMUM_LENGTH,
		    YouTubeDistributionField::TV_METADATA_SEASON => self::TV_METADATA_SEASON_MAXIMUM_LENGTH,
		    YouTubeDistributionField::TV_METADATA_SHOW_TITLE => self::TV_METADATA_SHOW_TITLE_MAXIMUM_LENGTH,
		    YouTubeDistributionField::TV_METADATA_TMS_ID => self::TV_METADATA_TMS_ID_MAXIMUM_LENGTH,
		    YouTubeDistributionField::MOVIE_METADATA_TITLE => self::MOVIE_METADATA_TITLE_MAXIMUM_LENGTH,
		    YouTubeDistributionField::MOVIE_METADATA_TMS_ID => self::MOVIE_METADATA_TMS_ID_MAXIMUM_LENGTH,
		);
		    		
		$inListOrNullFields = array (
		    YouTubeDistributionField::MEDIA_RATING => explode(',', self::MEDIA_RATING_VALID_VALUES),
		    YouTubeDistributionField::ALLOW_COMMENTS => explode(',', self::ALLOW_COMMENTS_VALID_VALUES),
		    YouTubeDistributionField::ALLOW_EMBEDDING => explode(',', self::ALLOW_EMBEDDING_VALID_VALUES),
		    YouTubeDistributionField::ALLOW_RATINGS => explode(',', self::ALLOW_RATINGS_VALID_VALUES),
		    YouTubeDistributionField::ALLOW_RESPONSES => explode(',', self::ALLOW_RESPONSES_VALID_VALUES),
		    YouTubeDistributionField::ADVERTISING_INVIDEO => explode(',', self::ADVERTISING_INVIDEO_VALID_VALUES),
		    YouTubeDistributionField::ADVERTISING_ADSENSE_FOR_VIDEO => explode(',', self::ADVERTISING_ADSENSE_FOR_VIDEO_VALUES),
		    YouTubeDistributionField::DISTRIBUTION_RESTRICTION_DISTRIBUTION_RULE => explode(',', self::DISTRIBUTION_RESTRICTION_DISTRIBUTION_RULE_VALUES),
		    YouTubeDistributionField::URGENT_REFERENCE_FILE => explode(',', self::URGENT_REFERENCE_FILE_VALUES),
		    YouTubeDistributionField::KEEP_FINGERPRINT => explode(',', self::KEEP_FINGERPRINT_VALUES),
		);
		
		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if (!$allFieldValues || !is_array($allFieldValues)) {
		    KalturaLog::err('Error getting field values from entry distribution id ['.$entryDistribution->getId().'] profile id ['.$this->getId().']');
		    return $validationErrors;
		}
		
		$validationErrors = array_merge($validationErrors, $this->validateMaxLength($maxLengthFields, $allFieldValues, $action));
		$validationErrors = array_merge($validationErrors, $this->validateInListOrNull($inListOrNullFields, $allFieldValues, $action));

	    $fieldName = YouTubeDistributionField::NOTIFICATION_EMAIL;
		$value = $allFieldValues[$fieldName];
		//multiple email support
		$values = explode(' ',$value);
		foreach ($values as $val)
		{
			if (!is_null($val) && !kString::isEmailString($val))
			{
				$errorMsg = $this->getUserFriendlyFieldName($fieldName).' value must be an email string [value:'.$val.']';
			    $validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $this->getUserFriendlyFieldName($fieldName));
				$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
				$validationError->setValidationErrorParam($errorMsg);
				$validationErrors[] = $validationError;
			}
		}

		$fieldName = YouTubeDistributionField::MEDIA_KEYWORDS;
		$keywordStr = $allFieldValues[$fieldName];
		if ($keywordStr)
		{
			$keywordsArray = explode(',',$keywordStr);
			foreach($keywordsArray as $keyword)
			{
				if (!$keyword)
				{
					$errorMsg = 'Keyword cannot be empty';
					$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $this->getUserFriendlyFieldName($fieldName));
					$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
					$validationError->setValidationErrorParam($errorMsg);
					$validationErrors[] = $validationError;
					continue;
				}
				if (strlen($keyword) < self::MEDIA_KEYWORDS_MINIMUM_LENGTH_EACH_KEYWORD
					|| strlen($keyword) > self::MEDIA_KEYWORDS_MAXIMUM_LENGTH_EACH_KEYWORD)
				{
					$errorMsg = 'Keyword "'.$keyword.'" must be at least two characters long and may not be longer than 30 characters';
					$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $this->getUserFriendlyFieldName($fieldName));
					$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
					$validationError->setValidationErrorParam($errorMsg);
					$validationErrors[] = $validationError;
				}
			}
		}
		
		//TODO: check if MEDIA_CATEGORY is a valid YouTube category according to YouTube's XML.

		return $validationErrors;
	}

	public function getFeedSpecVersion()	 {return $this->getFromCustomData(self::CUSTOM_DATA_FEED_SPEC_VERSION);}
	public function getUsername()			 {return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getOwnerName()			 {return $this->getFromCustomData(self::CUSTOM_DATA_OWNER_NAME);}
	public function getNotificationEmail()	 {return $this->getFromCustomData(self::CUSTOM_DATA_NOTIFICATION_EMAIL);}
	public function getSftpHost()			 {return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_HOST);}
	public function getSftpPort()			 {return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_PORT);}
	public function getSftpLogin()			 {return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_LOGIN);}
	public function getSftpPublicKey()		 {return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_PUBLIC_KEY);}
	public function getSftpPrivateKey()		 {return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_PRIVATE_KEY);}
	public function getSftpBaseDir()		 {return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_BASE_DIRECTORY);}
	public function getDefaultCategory()	 {return $this->getFromCustomData(self::CUSTOM_DATA_DEFAULT_CATEGORY);}
	public function getAllowComments()		 {return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_COMMENTS);}
	public function getAllowEmbedding()		 {return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_EMBEDDING);}
	public function getAllowRatings()		 {return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_RATINGS);}
	public function getAllowResponses()		 {return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_RESPONSES);}
	public function getCommercialPolicy()	 {return $this->getFromCustomData(self::CUSTOM_DATA_COMMENRCIAL_POLICY);}
	public function getUgcPolicy()			 {return $this->getFromCustomData(self::CUSTOM_DATA_UGC_POLICY);}
	public function getTarget()				 {return $this->getFromCustomData(self::CUSTOM_DATA_TARGET);}
    public function getAdServerPartnerId()   {return $this->getFromCustomData(self::CUSTOM_DATA_AD_SERVER_PARTNER_ID);}
	public function getEnableAdServer()      {return $this->getFromCustomData(self::CUSTOM_DATA_ENABLE_AD_SERVER);}
    public function getAllowPreRollAds()      {return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_PRE_ROLL_ADS);}
    public function getAllowPostRollAds()      {return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_POST_ROLL_ADS);}
	public function getStrict()					{return $this->getFromCustomData(self::CUSTOM_DATA_STRICT);}
	public function getOverrideManualEdits()	{return $this->getFromCustomData(self::CUSTOM_DATA_OVERRIDE_MANUAL_EDITS);}
	public function getUrgentReference()		{return $this->getFromCustomData(self::CUSTOM_DATA_URGENT_REFERENCE);}
	public function getAllowSyndication()		{return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_SYNDICATION);}
	public function getHideViewCount()			{return $this->getFromCustomData(self::CUSTOM_DATA_HIDE_VIEW_COUNT);}
	public function getAllowAdsenseForVideo()	{return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_ADSENSE_FOR_VIDEO);}
	public function getAllowInvideo()			{return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_INVIDEO);}
	public function getAllowMidRollAds()		{return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_MID_ROLL_ADS);}
	public function getInstreamStandard()		{return $this->getFromCustomData(self::CUSTOM_DATA_INSTREAM_STANDARD);}
	public function getInstreamTrueview()		{return $this->getFromCustomData(self::CUSTOM_DATA_INSTREAM_TRUEVIEW);}
	public function getClaimType()				{return $this->getFromCustomData(self::CUSTOM_DATA_CLAIM_TYPE);}
	public function getBlockOutsideOwnership()	{return $this->getFromCustomData(self::CUSTOM_DATA_BLOCK_OUTSIDE_OWNERSHIP);}
	public function getCaptionAutosync()		{return $this->getFromCustomData(self::CUSTOM_DATA_CAPTION_AUTOSYNC);}
	public function getDeleteReference()		{return $this->getFromCustomData(self::CUSTOM_DATA_DELETE_REFERENCE);}
	public function getReleaseClaims()			{return $this->getFromCustomData(self::CUSTOM_DATA_RELEASE_CLAIMS);}
	public function getPrivacyStatus()			{return $this->getFromCustomData(self::CUSTOM_DATA_PRIVACY_STATUS, null, 'public');}
	public function getEnableContentId()		{return $this->getFromCustomData(self::CUSTOM_DATA_ENABLE_CONTENT_ID);}
	public function getThirdPartyAds()		    {return $this->getFromCustomData(self::CUSTOM_DATA_THIRD_PARTY_ADS);}
	public function getProductListingAds()		{return $this->getFromCustomData(self::CUSTOM_DATA_PRODUCT_LISTING_ADS);}
	public function getDomainWhitelist($v)		{return $this->getFromCustomData(self::CUSTOM_DATA_DOMAIN_WHITELIST);}
	public function getNotifySubscribers($v)	{return $this->getFromCustomData(self::CUSTOM_DATA_NOTIFY_SUBSCRIBERS);}

	public function setFeedSpecVersion($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_SPEC_VERSION, $v);}
	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setOwnerName($v)			{$this->putInCustomData(self::CUSTOM_DATA_OWNER_NAME, $v);}
	public function setNotificationEmail($v)	{$this->putInCustomData(self::CUSTOM_DATA_NOTIFICATION_EMAIL, $v);}
	public function setSftpHost($v)				{$this->putInCustomData(self::CUSTOM_DATA_SFTP_HOST, $v);}
	public function setSftpPort($v)				{$this->putInCustomData(self::CUSTOM_DATA_SFTP_PORT, $v);}
	public function setSftpLogin($v)			{$this->putInCustomData(self::CUSTOM_DATA_SFTP_LOGIN, $v);}
	public function setSftpPublicKey($v)		{$this->putInCustomData(self::CUSTOM_DATA_SFTP_PUBLIC_KEY, $v);}
	public function setSftpPrivateKey($v)		{$this->putInCustomData(self::CUSTOM_DATA_SFTP_PRIVATE_KEY, $v);}
	public function setSftpBaseDir($v)			{$this->putInCustomData(self::CUSTOM_DATA_SFTP_BASE_DIRECTORY, $v);}
	public function setDefaultCategory($v)		{$this->putInCustomData(self::CUSTOM_DATA_DEFAULT_CATEGORY, $v);}
	public function setAllowComments($v)		{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_COMMENTS, $v);}
	public function setAllowEmbedding($v)		{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_EMBEDDING, $v);}
	public function setAllowRatings($v)			{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_RATINGS, $v);}
	public function setAllowResponses($v)		{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_RESPONSES, $v);}
	public function setCommercialPolicy($v)		{$this->putInCustomData(self::CUSTOM_DATA_COMMENRCIAL_POLICY, $v);}
	public function setUgcPolicy($v)			{$this->putInCustomData(self::CUSTOM_DATA_UGC_POLICY, $v);}
	public function setTarget($v)				{$this->putInCustomData(self::CUSTOM_DATA_TARGET, $v);}
    public function setAdServerPartnerId($v)	{$this->putInCustomData(self::CUSTOM_DATA_AD_SERVER_PARTNER_ID, $v);}
    public function setEnableAdServer($v)	    {$this->putInCustomData(self::CUSTOM_DATA_ENABLE_AD_SERVER, $v);}
    public function setAllowPreRollAds($v)	    {$this->putInCustomData(self::CUSTOM_DATA_ALLOW_PRE_ROLL_ADS, $v);}
    public function setAllowPostRollAds($v)	    {$this->putInCustomData(self::CUSTOM_DATA_ALLOW_POST_ROLL_ADS, $v);}
	public function setStrict($v)				{$this->putInCustomData(self::CUSTOM_DATA_STRICT, $v);}
	public function setOverrideManualEdits($v)	{$this->putInCustomData(self::CUSTOM_DATA_OVERRIDE_MANUAL_EDITS, $v);}
	public function setUrgentReference($v)		{$this->putInCustomData(self::CUSTOM_DATA_URGENT_REFERENCE, $v);}
	public function setAllowSyndication($v)		{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_SYNDICATION, $v);}
	public function setHideViewCount($v)		{$this->putInCustomData(self::CUSTOM_DATA_HIDE_VIEW_COUNT, $v);}
	public function setAllowAdsenseForVideo($v)	{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_ADSENSE_FOR_VIDEO, $v);}
	public function setAllowInvideo($v)			{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_INVIDEO, $v);}
	public function setAllowMidRollAds($v)		{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_MID_ROLL_ADS, $v);}
	public function setInstreamStandard($v)		{$this->putInCustomData(self::CUSTOM_DATA_INSTREAM_STANDARD, $v);}
	public function setInstreamTrueview($v)		{$this->putInCustomData(self::CUSTOM_DATA_INSTREAM_TRUEVIEW, $v);}
	public function setClaimType($v)			{$this->putInCustomData(self::CUSTOM_DATA_CLAIM_TYPE, $v);}
	public function setBlockOutsideOwnership($v){$this->putInCustomData(self::CUSTOM_DATA_BLOCK_OUTSIDE_OWNERSHIP, $v);}
	public function setCaptionAutosync($v)		{$this->putInCustomData(self::CUSTOM_DATA_CAPTION_AUTOSYNC, $v);}
	public function setDeleteReference($v)		{$this->putInCustomData(self::CUSTOM_DATA_DELETE_REFERENCE, $v);}
	public function setReleaseClaims($v)		{$this->putInCustomData(self::CUSTOM_DATA_RELEASE_CLAIMS, $v);}
	public function setPrivacyStatus($v)		{$this->putInCustomData(self::CUSTOM_DATA_PRIVACY_STATUS, $v);}
	public function setEnableContentId($v)		{$this->putInCustomData(self::CUSTOM_DATA_ENABLE_CONTENT_ID, $v);}
	public function setProductListingAds($v)	{$this->putInCustomData(self::CUSTOM_DATA_PRODUCT_LISTING_ADS, $v);}
	public function setThirdPartyAds($v)		{$this->putInCustomData(self::CUSTOM_DATA_THIRD_PARTY_ADS, $v);}
	public function setDomainWhitelist($v)		{$this->putInCustomData(self::CUSTOM_DATA_DOMAIN_WHITELIST, $v);}
	public function setNotifySubscribers($v)	{$this->putInCustomData(self::CUSTOM_DATA_NOTIFY_SUBSCRIBERS, $v);}

	protected function getDefaultFieldConfigArray()
	{
	    $fieldConfigArray = parent::getDefaultFieldConfigArray();
	      
	    // media fields
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::MEDIA_TITLE);
	    $fieldConfig->setUserFriendlyFieldName('Entry name');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::NAME));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::MEDIA_DESCRIPTION);
	    $fieldConfig->setUserFriendlyFieldName('Entry description');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::DESCRIPTION));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	        
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::MEDIA_KEYWORDS);
	    $fieldConfig->setUserFriendlyFieldName('Entry tags');
	    $fieldConfig->setEntryMrssXslt(
	    			'<xsl:for-each select="tags/tag">
						<xsl:if test="position() &gt; 1">
							<xsl:text>,</xsl:text>
						</xsl:if>
						<xsl:value-of select="." />
					</xsl:for-each>');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::TAGS));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::MEDIA_RATING);
	    $fieldConfig->setUserFriendlyFieldName('Media rating');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::MEDIA_CATEGORY);
	    $fieldConfig->setUserFriendlyFieldName('Media category');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/default_category" />');
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::WEB_METADATA_CUSTOM_ID);
	    $fieldConfig->setUserFriendlyFieldName('Entry ID');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::WEB_METADATA_NOTES);
	    $fieldConfig->setUserFriendlyFieldName('Web metadata notes');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::MOVIE_METADATA_CUSTOM_ID);
	    $fieldConfig->setUserFriendlyFieldName('Movie metadata custom ID');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::MOVIE_METADATA_DIRECTOR);
	    $fieldConfig->setUserFriendlyFieldName('Movie metadata director');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::MOVIE_METADATA_NOTES);
	    $fieldConfig->setUserFriendlyFieldName('Movie metadata notes');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::MOVIE_METADATA_TITLE);
	    $fieldConfig->setUserFriendlyFieldName('Movie metadata title');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::MOVIE_METADATA_TMS_ID);
	    $fieldConfig->setUserFriendlyFieldName('Movie metadata TMS ID');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::DATE_RECORDED);
	    $fieldConfig->setUserFriendlyFieldName('Date recorded');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
        $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::START_TIME);
	    $fieldConfig->setUserFriendlyFieldName('Distribution sunrise');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunrise" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::END_TIME);
	    $fieldConfig->setUserFriendlyFieldName('Distribution sunset');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::URGENT_REFERENCE_FILE);
	    $fieldConfig->setUserFriendlyFieldName('Urgent reference file');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::KEEP_FINGERPRINT);
	    $fieldConfig->setUserFriendlyFieldName('Keep fingerprint');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    // community fields
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::ALLOW_COMMENTS);
	    $fieldConfig->setUserFriendlyFieldName('Allow comments');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_comments" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::ALLOW_RESPONSES);
	    $fieldConfig->setUserFriendlyFieldName('Allow responses');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_responses" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::ALLOW_RATINGS);
	    $fieldConfig->setUserFriendlyFieldName('Allow ratings');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_ratings" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::ALLOW_EMBEDDING);
	    $fieldConfig->setUserFriendlyFieldName('Allow embedding');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_embedding" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    // youtube extra data
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::POLICY_COMMERCIAL);
	    $fieldConfig->setUserFriendlyFieldName('Commercial policy');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/commerical_policy" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::POLICY_UGC);
	    $fieldConfig->setUserFriendlyFieldName('UGC policy');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/ugc_policy" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::NOTIFICATION_EMAIL);
	    $fieldConfig->setUserFriendlyFieldName('Notification Email');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/notification_email" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::ACCOUNT_USERNAME);
	    $fieldConfig->setUserFriendlyFieldName('Account username');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/account_username" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::ACCOUNT_PASSWORD);
	    $fieldConfig->setUserFriendlyFieldName('Account password');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>'); // the password should not be added in contributeMRSS
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::OWNER_NAME);
	    $fieldConfig->setUserFriendlyFieldName('Account username');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/account_username" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::TARGET);
	    $fieldConfig->setUserFriendlyFieldName('YouTube target');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/target" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::LANGUAGE);
	    $fieldConfig->setUserFriendlyFieldName('YouTube language');
	    $fieldConfig->setEntryMrssXslt('<xsl:text>en</xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::TV_METADATA_CUSTOM_ID);
	    $fieldConfig->setUserFriendlyFieldName('TV metadata custom id');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::TV_METADATA_EPISODE);
	    $fieldConfig->setUserFriendlyFieldName('TV metadata episode');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::TV_METADATA_EPISODE_TITLE);
	    $fieldConfig->setUserFriendlyFieldName('TV metadata episode title');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::TV_METADATA_SHOW_TITLE);
	    $fieldConfig->setUserFriendlyFieldName('TV metadata show title');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::TV_METADATA_SEASON);
	    $fieldConfig->setUserFriendlyFieldName('TV metadata season');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::TV_METADATA_NOTES);
	    $fieldConfig->setUserFriendlyFieldName('TV metadata notes');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::TV_METADATA_TMS_ID);
	    $fieldConfig->setUserFriendlyFieldName('TV metadata TMS ID');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::PLAYLISTS);
	    $fieldConfig->setUserFriendlyFieldName('YouTube playlists');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/YouTubePlaylist" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array("/*[local-name()='metadata']/*[local-name()='YouTubePlaylist']"));
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::ADVERTISING_ADSENSE_FOR_VIDEO);
	    $fieldConfig->setUserFriendlyFieldName('Advertising adsense for video');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::ADVERTISING_INVIDEO);
	    $fieldConfig->setUserFriendlyFieldName('Advertising in video');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::THIRD_PARTY_AD_SERVER_AD_TYPE);
	    $fieldConfig->setUserFriendlyFieldName('Third party ad server ad type');
	    $fieldConfig->setEntryMrssXslt('<xsl:text>1</xsl:text>');
	    $fieldConfig->setUpdateOnChange(false);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::THIRD_PARTY_AD_SERVER_PARTNER_ID);
	    $fieldConfig->setUserFriendlyFieldName('Third party ad server partner ID');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/ad_server_partner_id" />');
	    $fieldConfig->setUpdateOnChange(false);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::THIRD_PARTY_AD_SERVER_VIDEO_ID);
	    $fieldConfig->setUserFriendlyFieldName('Entry ID');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
	    $fieldConfig->setUpdateOnChange(false);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::ADVERTISING_ALLOW_PRE_ROLL_ADS);
	    $fieldConfig->setUserFriendlyFieldName('Allow Pre Roll Ads');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_pre_roll_ads" />');
	    $fieldConfig->setUpdateOnChange(false);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(YouTubeDistributionField::ADVERTISING_ALLOW_MID_ROLL_ADS);
		$fieldConfig->setUserFriendlyFieldName('Allow Mid Roll Ads');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_mid_roll_ads" />');
		$fieldConfig->setUpdateOnChange(false);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::ADVERTISING_ALLOW_POST_ROLL_ADS);
	    $fieldConfig->setUserFriendlyFieldName('Allow Post Roll Ads');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_post_roll_ads" />');
	    $fieldConfig->setUpdateOnChange(false);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::LOCATION_COUNTRY);
	    $fieldConfig->setUserFriendlyFieldName('Location country');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::LOCATION_LOCATION_TEXT);
	    $fieldConfig->setUserFriendlyFieldName('Location text');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::LOCATION_ZIP_CODE);
	    $fieldConfig->setUserFriendlyFieldName('Location zip code');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(YouTubeDistributionField::DISTRIBUTION_RESTRICTION_DISTRIBUTION_RULE);
	    $fieldConfig->setUserFriendlyFieldName('Distribution restriction rule');
	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_ACTOR, 'Asset actor', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_BROADCASTER, 'Asset broadcaster', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_CONTENT_TYPE, 'Asset content type', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_CUSTOM_ID, 'Asset custom id', '<xsl:value-of select="string(entryId)" />');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_DESCRIPTION, 'Asset description', '<xsl:value-of select="string(description)" />');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_DIRECTOR, 'Asset director', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_EIDR, 'Asset EIDR', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_END_YEAR, 'Asset end year', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_EPISODE, 'Asset episode', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_GENRE, 'Asset genre', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_GRID, 'Asset GRid', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_ISAN, 'Asset ISAN', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_KEYWORDS, 'Asset keywords', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_NOTES, 'Asset notes', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_ORIGINAL_RELEASE_DATE, 'Asset original release date', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_ORIGINAL_RELEASE_MEDIUM, 'Asset original medium', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_PRODUCER, 'Asset producer', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_RATING_SYSTEM, 'Asset rating system', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_RATING_VALUE, 'Asset rating value', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_SEASON, 'Asset season', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_SHOW_AND_MOVIE_PROGRAMMING, 'Asset show and movie programming', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_SHOW_TITLE, 'Asset show title', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_SPOKEN_LANGUAGE, 'Asset spoken language', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_START_YEAR, 'Asset start year', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_SUBTITLED_LANGUAGE, 'Asset subtitles language', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_TITLE, 'Asset title', '<xsl:value-of select="string(title)" />');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_TYPE, 'Asset type', '<xsl:text>web</xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_TMS_ID, 'Asset TMS ID', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_UPC, 'Asset UPC', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_URL, 'Asset URL', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_WRITER, 'Asset Writer', '<xsl:text></xsl:text>');

		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::VIDEO_ALLOW_COMMENT_RATINGS, 'Video allow comment ratings', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::VIDEO_ALLOW_SYNDICATION, 'Video allow syndication', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::VIDEO_CHANNEL, 'Video channel', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::VIDEO_HIDE_VIEW_COUNT, 'Video hide view count', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::VIDEO_DOMAIN_BLACK_LIST, 'Video domain black list', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::VIDEO_DOMAIN_WHITE_LIST, 'Video domain white list', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::VIDEO_NOTIFY_SUBSCRIBERS, 'Video notify subscribers', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::VIDEO_PUBLIC, 'Video public', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::VIDEO_CHANNEL, 'Video channel', '<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/account_username" />');

		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::CLAIM_TYPE, 'Claim type', '<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/claim_type" />');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::CLAIM_BLOCK_OUTSIDE_OWNERSHIP, 'Video block outside ownership', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ADVERTISING_INSTREAM_STANDARD, 'Instream standard', '<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/instream_standard" />');

		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::DISABLE_FINGERPRINTING, 'Disable fingerprinting/claiming', '<xsl:text></xsl:text>');

		switch ($this->getFeedSpecVersion())
		{
			case YouTubeDistributionFeedSpecVersion::VERSION_1:
			{
				$this->removeDistributionFieldConfigs($fieldConfigArray, $this->specV2OnlyFields);
				break;
			}
			case YouTubeDistributionFeedSpecVersion::VERSION_2:
			{
				$this->removeDistributionFieldConfigs($fieldConfigArray, $this->specV1OnlyFields);
				break;
			}
			case YouTubeDistributionFeedSpecVersion::VERSION_3:
			{
				$this->addV3RelevantFields($fieldConfigArray);
				$this->removeDistributionFieldConfigs($fieldConfigArray, $this->specV3NonRelevantFields);
				break;
			}
			default:
			{
				$this->removeDistributionFieldConfigs($fieldConfigArray, $this->specV2OnlyFields);
				break;
			}
		}
	    return $fieldConfigArray;
	}

	protected function addV3RelevantFields(array &$fieldConfigArray)
	{
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ASSET_LABLES, 'Asset lables', '<xsl:text></xsl:text>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::REQUIRE_PAID_SUBSCRIPTION_TO_VIEW, 'Require paid subscription to view', '<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/require_paid_subscription_to_view"/>');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ENTRY_PRIVACY_STATUS, 'Entry Privacy Status', '<xsl:choose>
                    	<xsl:when test="customData/metadata/'.self::METADATA_FIELD_PRIVACY_STATUS.' != \'\'">
                    		<xsl:value-of select="customData/metadata/'.self::METADATA_FIELD_PRIVACY_STATUS.'" />
                    	</xsl:when>
                    	<xsl:otherwise>
                    		<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/privacy_status" />
                    	</xsl:otherwise>
                    </xsl:choose>');

		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::THIRD_PARTY_ADS, 'Third party ads', '<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/third_party_ads" />');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::PRODUCT_LISTING_ADS, 'Product listing ads', '<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/product_listing_ads" />');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ADVERTISING_INSTREAM_TRUEVIEW, 'Product listing ads', '<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/instream_trueview" />');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ADVERTISING_ALLOW_INVIDEO, 'Product listing ads', '<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_invideo" />');
		$this->addDistributionFieldConfig($fieldConfigArray, YouTubeDistributionField::ADVERTISING_ALLOW_ADSENSE_FOR_VIDEO, 'Product listing ads', '<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_adsense_for_video" />');


	}

	protected function addDistributionFieldConfig(array &$array, $name, $friendlyName, $xslt, $required = DistributionFieldRequiredStatus::NOT_REQUIRED, $updateOnChange = false, $updateOnParams = array())
	{
		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName($name);
		$fieldConfig->setUserFriendlyFieldName($friendlyName);
		$fieldConfig->setEntryMrssXslt($xslt);
		if ($updateOnChange)
			$fieldConfig->setUpdateOnChange(true);
		$fieldConfig->setIsRequired($required);
		$fieldConfig->setUpdateParams($updateOnParams);
		$array[$name] = $fieldConfig;
	}

	protected function removeDistributionFieldConfigs(array &$fieldConfigArray, array $fields)
	{
		foreach($fields as $field)
		{
			if (isset($fieldConfigArray[$field]))
				unset($fieldConfigArray[$field]);
		}
	}
	
	public function getGoogleOAuth2Data()
	{
		$appId = YouTubeDistributionPlugin::GOOGLE_APP_ID;
		$subId = $this->getId();
		
		$partner = PartnerPeer::retrieveByPK($this->getPartnerId());
		return $partner->getGoogleOAuth2($appId, $subId);
	}
	
	public function getApiAuthorizeUrl()
	{
		$tokenData = $this->getGoogleOAuth2Data();
		if(!is_null($tokenData))
		{
			return null;
		}
	
		$appId = YouTubeDistributionPlugin::GOOGLE_APP_ID;
		$subId = $this->getId();
					
		$url = kConf::get('apphome_url');
		$url .= "/index.php/extservices/googleoauth2/ytid/$appId/subid/$subId";
		$url .= "?partnerId=".$this->getPartnerId();
		return $url;
	}
}