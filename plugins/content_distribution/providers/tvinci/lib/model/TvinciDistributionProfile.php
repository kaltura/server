<?php
/**
 * @package plugins.tvinciDistribution
 * @subpackage model
 */
class TvinciDistributionProfile extends ConfigurableDistributionProfile
{
// 	const CUSTOM_DATA_FEED_SPEC_VERSION = 'apiVersion';
 	const CUSTOM_DATA_INGEST_URL = 'ingestUrl';
 	const CUSTOM_DATA_USERNAME = 'username';
 	const CUSTOM_DATA_PASSWORD = 'password';
// 	const CUSTOM_DATA_BROADCASTER_NAME = 'broadcasterName';
// 	const CUSTOM_DATA_USERNAME = 'username';
// 	const CUSTOM_DATA_OWNER_NAME = 'ownerName';
// 	const CUSTOM_DATA_NOTIFICATION_EMAIL = 'notificationEmail';
// 	const CUSTOM_DATA_SFTP_HOST = 'sftpHost';
// 	const CUSTOM_DATA_SFTP_PORT = 'sftpPort';
// 	const CUSTOM_DATA_SFTP_LOGIN = 'sftpLogin';
// 	const CUSTOM_DATA_SFTP_PUBLIC_KEY = 'sftpPublicKey';
// 	const CUSTOM_DATA_SFTP_PRIVATE_KEY = 'sftpPrivateKey';
// 	const CUSTOM_DATA_SFTP_BASE_DIRECTORY = 'sftpBaseDir';
// 	const CUSTOM_DATA_DEFAULT_CATEGORY = 'defaultCategory';
// 	const CUSTOM_DATA_ALLOW_COMMENTS = 'allowComments';
// 	const CUSTOM_DATA_ALLOW_EMBEDDING = 'allowEmbedding';
// 	const CUSTOM_DATA_ALLOW_RATINGS = 'allowRatings';
// 	const CUSTOM_DATA_ALLOW_RESPONSES = 'allowResponses';
// 	const CUSTOM_DATA_COMMENRCIAL_POLICY = 'commercialPolicy';
// 	const CUSTOM_DATA_UGC_POLICY = 'ugcPolicy';
// 	const CUSTOM_DATA_TARGET = 'target';
// 	const CUSTOM_DATA_AD_SERVER_PARTNER_ID = 'adServerPartnerId';
// 	const CUSTOM_DATA_ENABLE_AD_SERVER = 'enableAdServer';
// 	const CUSTOM_DATA_ALLOW_PRE_ROLL_ADS = 'allowPreRollAds';
// 	const CUSTOM_DATA_ALLOW_POST_ROLL_ADS = 'allowPostRollAds';
// 	const CUSTOM_DATA_STRICT = 'strict';
// 	const CUSTOM_DATA_OVERRIDE_MANUAL_EDITS = 'overrideManualEdits';
// 	const CUSTOM_DATA_URGENT_REFERENCE = 'urgentReference';
// 	const CUSTOM_DATA_ALLOW_SYNDICATION = 'allowSyndication';
// 	const CUSTOM_DATA_HIDE_VIEW_COUNT = 'hideViewCount';
// 	const CUSTOM_DATA_ALLOW_ADSENSE_FOR_VIDEO = 'allowAdsenseForVideo';
// 	const CUSTOM_DATA_ALLOW_INVIDEO = 'allowInvideo';
// 	const CUSTOM_DATA_ALLOW_MID_ROLL_ADS = 'allowMidRollAds';
// 	const CUSTOM_DATA_INSTREAM_STANDARD = 'instreamStandard';
// 	const CUSTOM_DATA_INSTREAM_TRUEVIEW = 'instreamTrueview';
// 	const CUSTOM_DATA_CLAIM_TYPE = 'claimType';
// 	const CUSTOM_DATA_BLOCK_OUTSIDE_OWNERSHIP = 'blockOutsideOwnership';
// 	const CUSTOM_DATA_CAPTION_AUTOSYNC = 'captionAutosync';
// 	const CUSTOM_DATA_DELETE_REFERENCE = 'deleteReference';
// 	const CUSTOM_DATA_RELEASE_CLAIMS = 'releaseClaims';
	
// 	// validations
// 	const MEDIA_TITLE_MAXIMUM_LENGTH = 100;
// 	const MEDIA_DESCRIPTION_MAXIMUM_LENGTH = 5000;
// 	const MEDIA_KEYWORDS_MAXIMUM_TOTAL_LENGTH = 500;
// 	const MEDIA_KEYWORDS_MINIMUM_LENGTH_EACH_KEYWORD = 2;
// 	const MEDIA_KEYWORDS_MAXIMUM_LENGTH_EACH_KEYWORD = 30;
// 	const METADATA_CUSTOM_ID_MAXIMUM_LENGTH = 64;
// 	const TV_METADATA_EPISODE_MAXIMUM_LENGTH = 16;
// 	const TV_METADATA_SEASON_MAXIMUM_LENGTH = 16;
// 	const TV_METADATA_EPISODE_TITLE_MAXIMUM_LENGTH = 64;
// 	const TV_METADATA_SHOW_TITLE_MAXIMUM_LENGTH = 64;
// 	const TV_METADATA_TMS_ID_MAXIMUM_LENGTH = 14;
// 	const MOVIE_METADATA_TITLE_MAXIMUM_LENGTH = 64;
// 	const MOVIE_METADATA_TMS_ID_MAXIMUM_LENGTH = 14;
	
// 	const MEDIA_RATING_VALID_VALUES = 'adult,nonadult';
// 	const ALLOW_COMMENTS_VALID_VALUES = 'Always,Approve,Never';
// 	const ALLOW_RESPONSES_VALID_VALUES = 'Always,Approve,Never';
// 	const ALLOW_EMBEDDING_VALID_VALUES = 'true,false';
// 	const ALLOW_RATINGS_VALID_VALUES = 'true,false';
// 	const ADVERTISING_INVIDEO_VALID_VALUES = 'Allow,Deny';
// 	const ADVERTISING_ADSENSE_FOR_VIDEO_VALUES = 'Allow,Deny';
// 	const DISTRIBUTION_RESTRICTION_DISTRIBUTION_RULE_VALUES = 'Allow,Deny';
// 	const URGENT_REFERENCE_FILE_VALUES = 'yes,no';
// 	const KEEP_FINGERPRINT_VALUES = 'yes,no';

// 	protected $specV1OnlyFields = array(
// 		TvinciDistributionField::OWNER_NAME,
// 		TvinciDistributionField::TARGET,
// 		TvinciDistributionField::LANGUAGE,
// 		TvinciDistributionField::KEEP_FINGERPRINT,
// 		TvinciDistributionField::ACCOUNT_USERNAME,
// 		TvinciDistributionField::ACCOUNT_PASSWORD,
// 		TvinciDistributionField::WEB_METADATA_CUSTOM_ID,
// 		TvinciDistributionField::WEB_METADATA_NOTES,
// 		TvinciDistributionField::MOVIE_METADATA_CUSTOM_ID,
// 		TvinciDistributionField::MOVIE_METADATA_DIRECTOR,
// 		TvinciDistributionField::MOVIE_METADATA_TITLE,
// 		TvinciDistributionField::MOVIE_METADATA_TMS_ID,
// 		TvinciDistributionField::TV_METADATA_CUSTOM_ID,
// 		TvinciDistributionField::TV_METADATA_SHOW_TITLE,
// 		TvinciDistributionField::TV_METADATA_EPISODE,
// 		TvinciDistributionField::TV_METADATA_EPISODE_TITLE,
// 		TvinciDistributionField::TV_METADATA_NOTES,
// 		TvinciDistributionField::TV_METADATA_SEASON,
// 		TvinciDistributionField::TV_METADATA_TMS_ID,
// 	);

// 	protected $specV2OnlyFields = array(
// 		TvinciDistributionField::ADVERTISING_ALLOW_MID_ROLL_ADS,
// 		TvinciDistributionField::ASSET_TYPE,
// 		TvinciDistributionField::ASSET_OVERRIDE_MANUAL_EDITS,
// 		TvinciDistributionField::ASSET_ACTOR,
// 		TvinciDistributionField::ASSET_BROADCASTER,
// 		TvinciDistributionField::ASSET_CONTENT_TYPE,
// 		TvinciDistributionField::ASSET_CUSTOM_ID,
// 		TvinciDistributionField::ASSET_DESCRIPTION,
// 		TvinciDistributionField::ASSET_DIRECTOR,
// 		TvinciDistributionField::ASSET_EIDR,
// 		TvinciDistributionField::ASSET_END_YEAR,
// 		TvinciDistributionField::ASSET_EPISODE,
// 		TvinciDistributionField::ASSET_GENRE,
// 		TvinciDistributionField::ASSET_GRID,
// 		TvinciDistributionField::ASSET_ISAN,
// 		TvinciDistributionField::ASSET_KEYWORDS,
// 		TvinciDistributionField::ASSET_NOTES,
// 		TvinciDistributionField::ASSET_ORIGINAL_RELEASE_DATE,
// 		TvinciDistributionField::ASSET_ORIGINAL_RELEASE_MEDIUM,
// 		TvinciDistributionField::ASSET_PRODUCER,
// 		TvinciDistributionField::ASSET_RATING_SYSTEM,
// 		TvinciDistributionField::ASSET_RATING_VALUE,
// 		TvinciDistributionField::ASSET_SEASON,
// 		TvinciDistributionField::ASSET_SHOW_AND_MOVIE_PROGRAMMING,
// 		TvinciDistributionField::ASSET_SHOW_TITLE,
// 		TvinciDistributionField::ASSET_SPOKEN_LANGUAGE,
// 		TvinciDistributionField::ASSET_START_YEAR,
// 		TvinciDistributionField::ASSET_SUBTITLED_LANGUAGE,
// 		TvinciDistributionField::ASSET_TITLE,
// 		TvinciDistributionField::ASSET_TMS_ID,
// 		TvinciDistributionField::ASSET_UPC,
// 		TvinciDistributionField::ASSET_URL,
// 		TvinciDistributionField::ASSET_WRITER,
// 		TvinciDistributionField::VIDEO_ALLOW_COMMENT_RATINGS,
// 		TvinciDistributionField::VIDEO_ALLOW_SYNDICATION,
// 		TvinciDistributionField::VIDEO_CHANNEL,
// 		TvinciDistributionField::VIDEO_HIDE_VIEW_COUNT,
// 		TvinciDistributionField::VIDEO_DOMAIN_BLACK_LIST,
// 		TvinciDistributionField::VIDEO_DOMAIN_WHITE_LIST,
// 		TvinciDistributionField::VIDEO_NOTIFY_SUBSCRIBERS,
// 		TvinciDistributionField::VIDEO_PUBLIC,
// 		TvinciDistributionField::CLAIM_TYPE,
// 		TvinciDistributionField::CLAIM_BLOCK_OUTSIDE_OWNERSHIP,
// 		TvinciDistributionField::ADVERTISING_INSTREAM_STANDARD,
// 		TvinciDistributionField::DISABLE_FINGERPRINTING,
// 	);

	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return TvinciDistributionPlugin::getProvider();
	}
	
		
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
	    $validationErrors = parent::validateForSubmission($entryDistribution, $action);
		
		$maxLengthFields = array (
// 		    TvinciDistributionField::MEDIA_DESCRIPTION => self::MEDIA_DESCRIPTION_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::MEDIA_TITLE => self::MEDIA_TITLE_MAXIMUM_LENGTH,
// 			TvinciDistributionField::MEDIA_KEYWORDS => self::MEDIA_KEYWORDS_MAXIMUM_TOTAL_LENGTH,
// 		    TvinciDistributionField::WEB_METADATA_CUSTOM_ID => self::METADATA_CUSTOM_ID_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::MOVIE_METADATA_CUSTOM_ID => self::METADATA_CUSTOM_ID_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::TV_METADATA_CUSTOM_ID => self::METADATA_CUSTOM_ID_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::TV_METADATA_EPISODE => self::TV_METADATA_EPISODE_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::TV_METADATA_EPISODE_TITLE => self::TV_METADATA_EPISODE_TITLE_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::TV_METADATA_SEASON => self::TV_METADATA_SEASON_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::TV_METADATA_SHOW_TITLE => self::TV_METADATA_SHOW_TITLE_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::TV_METADATA_TMS_ID => self::TV_METADATA_TMS_ID_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::MOVIE_METADATA_TITLE => self::MOVIE_METADATA_TITLE_MAXIMUM_LENGTH,
// 		    TvinciDistributionField::MOVIE_METADATA_TMS_ID => self::MOVIE_METADATA_TMS_ID_MAXIMUM_LENGTH,
		);
		    		
		$inListOrNullFields = array (
// 		    TvinciDistributionField::MEDIA_RATING => explode(',', self::MEDIA_RATING_VALID_VALUES),
// 		    TvinciDistributionField::ALLOW_COMMENTS => explode(',', self::ALLOW_COMMENTS_VALID_VALUES),
// 		    TvinciDistributionField::ALLOW_EMBEDDING => explode(',', self::ALLOW_EMBEDDING_VALID_VALUES),
// 		    TvinciDistributionField::ALLOW_RATINGS => explode(',', self::ALLOW_RATINGS_VALID_VALUES),
// 		    TvinciDistributionField::ALLOW_RESPONSES => explode(',', self::ALLOW_RESPONSES_VALID_VALUES),
// 		    TvinciDistributionField::ADVERTISING_INVIDEO => explode(',', self::ADVERTISING_INVIDEO_VALID_VALUES),
// 		    TvinciDistributionField::ADVERTISING_ADSENSE_FOR_VIDEO => explode(',', self::ADVERTISING_ADSENSE_FOR_VIDEO_VALUES),
// 		    TvinciDistributionField::DISTRIBUTION_RESTRICTION_DISTRIBUTION_RULE => explode(',', self::DISTRIBUTION_RESTRICTION_DISTRIBUTION_RULE_VALUES),
// 		    TvinciDistributionField::URGENT_REFERENCE_FILE => explode(',', self::URGENT_REFERENCE_FILE_VALUES),
// 		    TvinciDistributionField::KEEP_FINGERPRINT => explode(',', self::KEEP_FINGERPRINT_VALUES),
		);
		
		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if (!$allFieldValues || !is_array($allFieldValues)) {
		    KalturaLog::err('Error getting field values from entry distribution id ['.$entryDistribution->getId().'] profile id ['.$this->getId().']');
		    return $validationErrors;
		}
		
		$validationErrors = array_merge($validationErrors, $this->validateMaxLength($maxLengthFields, $allFieldValues, $action));
		$validationErrors = array_merge($validationErrors, $this->validateInListOrNull($inListOrNullFields, $allFieldValues, $action));

// 	    $fieldName = TvinciDistributionField::NOTIFICATION_EMAIL;
// 		$value = $allFieldValues[$fieldName];
// 		//multiple email support
// 		$values = explode(' ',$value);
// 		foreach ($values as $val)
// 		{
// 			if (!is_null($val) && !kString::isEmailString($val))
// 			{
// 				$errorMsg = $this->getUserFriendlyFieldName($fieldName).' value must be an email string [value:'.$val.']';
// 			    $validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $this->getUserFriendlyFieldName($fieldName));
// 				$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
// 				$validationError->setValidationErrorParam($errorMsg);
// 				$validationErrors[] = $validationError;
// 			}
// 		}

// 		$fieldName = TvinciDistributionField::MEDIA_KEYWORDS;
// 		$keywordStr = $allFieldValues[$fieldName];
// 		if ($keywordStr)
// 		{
// 			$keywordsArray = explode(',',$keywordStr);
// 			foreach($keywordsArray as $keyword)
// 			{
// 				if (!$keyword)
// 				{
// 					$errorMsg = 'Keyword cannot be empty';
// 					$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $this->getUserFriendlyFieldName($fieldName));
// 					$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
// 					$validationError->setValidationErrorParam($errorMsg);
// 					$validationErrors[] = $validationError;
// 					continue;
// 				}
// 				if (strlen($keyword) < self::MEDIA_KEYWORDS_MINIMUM_LENGTH_EACH_KEYWORD
// 					|| strlen($keyword) > self::MEDIA_KEYWORDS_MAXIMUM_LENGTH_EACH_KEYWORD)
// 				{
// 					$errorMsg = 'Keyword "'.$keyword.'" must be at least two characters long and may not be longer than 30 characters';
// 					$validationError = $this->createValidationError($action, DistributionErrorType::INVALID_DATA, $this->getUserFriendlyFieldName($fieldName));
// 					$validationError->setValidationErrorType(DistributionValidationErrorType::CUSTOM_ERROR);
// 					$validationError->setValidationErrorParam($errorMsg);
// 					$validationErrors[] = $validationError;
// 				}
// 			}
// 		}
		
		//TODO: check if MEDIA_CATEGORY is a valid Tvinci category according to Tvinci's XML.
								
		return $validationErrors;
	}

// 	public function getFeedSpecVersion()	 {return $this->getFromCustomData(self::CUSTOM_DATA_FEED_SPEC_VERSION);}
	public function getIngestUrl()			 {return $this->getFromCustomData(self::CUSTOM_DATA_INGEST_URL);}
	public function getUsername()			 {return $this->getFromCustomData(self::CUSTOM_DATA_USERNAME);}
	public function getPassword()			 {return $this->getFromCustomData(self::CUSTOM_DATA_PASSWORD);}
// 	public function getBroadcasterName()	 {return $this->getFromCustomData(self::CUSTOM_DATA_BROADCASTER_NAME);}
// 	public function getOwnerName()			 {return $this->getFromCustomData(self::CUSTOM_DATA_OWNER_NAME);}
// 	public function getNotificationEmail()	 {return $this->getFromCustomData(self::CUSTOM_DATA_NOTIFICATION_EMAIL);}
// 	public function getSftpHost()			 {return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_HOST);}
// 	public function getSftpPort()			 {return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_PORT);}
// 	public function getSftpLogin()			 {return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_LOGIN);}
// 	public function getSftpPublicKey()		 {return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_PUBLIC_KEY);}
// 	public function getSftpPrivateKey()		 {return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_PRIVATE_KEY);}
// 	public function getSftpBaseDir()		 {return $this->getFromCustomData(self::CUSTOM_DATA_SFTP_BASE_DIRECTORY);}
// 	public function getDefaultCategory()	 {return $this->getFromCustomData(self::CUSTOM_DATA_DEFAULT_CATEGORY);}
// 	public function getAllowComments()		 {return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_COMMENTS);}
// 	public function getAllowEmbedding()		 {return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_EMBEDDING);}
// 	public function getAllowRatings()		 {return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_RATINGS);}
// 	public function getAllowResponses()		 {return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_RESPONSES);}
// 	public function getCommercialPolicy()	 {return $this->getFromCustomData(self::CUSTOM_DATA_COMMENRCIAL_POLICY);}
// 	public function getUgcPolicy()			 {return $this->getFromCustomData(self::CUSTOM_DATA_UGC_POLICY);}
// 	public function getTarget()				 {return $this->getFromCustomData(self::CUSTOM_DATA_TARGET);}
//     public function getAdServerPartnerId()   {return $this->getFromCustomData(self::CUSTOM_DATA_AD_SERVER_PARTNER_ID);}
// 	public function getEnableAdServer()      {return $this->getFromCustomData(self::CUSTOM_DATA_ENABLE_AD_SERVER);}
//     public function getAllowPreRollAds()      {return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_PRE_ROLL_ADS);}
//     public function getAllowPostRollAds()      {return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_POST_ROLL_ADS);}
// 	public function getStrict()					{return $this->getFromCustomData(self::CUSTOM_DATA_STRICT);}
// 	public function getOverrideManualEdits()	{return $this->getFromCustomData(self::CUSTOM_DATA_OVERRIDE_MANUAL_EDITS);}
// 	public function getUrgentReference()		{return $this->getFromCustomData(self::CUSTOM_DATA_URGENT_REFERENCE);}
// 	public function getAllowSyndication()		{return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_SYNDICATION);}
// 	public function getHideViewCount()			{return $this->getFromCustomData(self::CUSTOM_DATA_HIDE_VIEW_COUNT);}
// 	public function getAllowAdsenseForVideo()	{return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_ADSENSE_FOR_VIDEO);}
// 	public function getAllowInvideo()			{return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_INVIDEO);}
// 	public function getAllowMidRollAds()		{return $this->getFromCustomData(self::CUSTOM_DATA_ALLOW_MID_ROLL_ADS);}
// 	public function getInstreamStandard()		{return $this->getFromCustomData(self::CUSTOM_DATA_INSTREAM_STANDARD);}
// 	public function getInstreamTrueview()		{return $this->getFromCustomData(self::CUSTOM_DATA_INSTREAM_TRUEVIEW);}
// 	public function getClaimType()				{return $this->getFromCustomData(self::CUSTOM_DATA_CLAIM_TYPE);}
// 	public function getBlockOutsideOwnership()	{return $this->getFromCustomData(self::CUSTOM_DATA_BLOCK_OUTSIDE_OWNERSHIP);}
// 	public function getCaptionAutosync()		{return $this->getFromCustomData(self::CUSTOM_DATA_CAPTION_AUTOSYNC);}
// 	public function getDeleteReference()		{return $this->getFromCustomData(self::CUSTOM_DATA_DELETE_REFERENCE);}
// 	public function getReleaseClaims()			{return $this->getFromCustomData(self::CUSTOM_DATA_RELEASE_CLAIMS);}

// 	public function setFeedSpecVersion($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_SPEC_VERSION, $v);}
	public function setPublisher($v)			{$this->putInCustomData(self::CUSTOM_DATA_PUBLISHER, $v);}
	public function setIngestUrl($v)			{$this->putInCustomData(self::CUSTOM_DATA_INGEST_URL, $v);}
	public function setUsername($v)				{$this->putInCustomData(self::CUSTOM_DATA_USERNAME, $v);}
	public function setPassword($v)				{$this->putInCustomData(self::CUSTOM_DATA_PASSWORD, $v);}
// 	public function setBroadcasterName($v)		{$this->putInCustomData(self::CUSTOM_DATA_BROADCASTER_NAME, $v);}
// 	public function setOwnerName($v)			{$this->putInCustomData(self::CUSTOM_DATA_OWNER_NAME, $v);}
// 	public function setNotificationEmail($v)	{$this->putInCustomData(self::CUSTOM_DATA_NOTIFICATION_EMAIL, $v);}
// 	public function setSftpHost($v)				{$this->putInCustomData(self::CUSTOM_DATA_SFTP_HOST, $v);}
// 	public function setSftpPort($v)				{$this->putInCustomData(self::CUSTOM_DATA_SFTP_PORT, $v);}
// 	public function setSftpLogin($v)			{$this->putInCustomData(self::CUSTOM_DATA_SFTP_LOGIN, $v);}
// 	public function setSftpPublicKey($v)		{$this->putInCustomData(self::CUSTOM_DATA_SFTP_PUBLIC_KEY, $v);}
// 	public function setSftpPrivateKey($v)		{$this->putInCustomData(self::CUSTOM_DATA_SFTP_PRIVATE_KEY, $v);}
// 	public function setSftpBaseDir($v)			{$this->putInCustomData(self::CUSTOM_DATA_SFTP_BASE_DIRECTORY, $v);}
// 	public function setDefaultCategory($v)		{$this->putInCustomData(self::CUSTOM_DATA_DEFAULT_CATEGORY, $v);}
// 	public function setAllowComments($v)		{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_COMMENTS, $v);}
// 	public function setAllowEmbedding($v)		{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_EMBEDDING, $v);}
// 	public function setAllowRatings($v)			{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_RATINGS, $v);}
// 	public function setAllowResponses($v)		{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_RESPONSES, $v);}
// 	public function setCommercialPolicy($v)		{$this->putInCustomData(self::CUSTOM_DATA_COMMENRCIAL_POLICY, $v);}
// 	public function setUgcPolicy($v)			{$this->putInCustomData(self::CUSTOM_DATA_UGC_POLICY, $v);}
// 	public function setTarget($v)				{$this->putInCustomData(self::CUSTOM_DATA_TARGET, $v);}
//     public function setAdServerPartnerId($v)	{$this->putInCustomData(self::CUSTOM_DATA_AD_SERVER_PARTNER_ID, $v);}
//     public function setEnableAdServer($v)	    {$this->putInCustomData(self::CUSTOM_DATA_ENABLE_AD_SERVER, $v);}
//     public function setAllowPreRollAds($v)	    {$this->putInCustomData(self::CUSTOM_DATA_ALLOW_PRE_ROLL_ADS, $v);}
//     public function setAllowPostRollAds($v)	    {$this->putInCustomData(self::CUSTOM_DATA_ALLOW_POST_ROLL_ADS, $v);}
// 	public function setStrict($v)				{$this->putInCustomData(self::CUSTOM_DATA_STRICT, $v);}
// 	public function setOverrideManualEdits($v)	{$this->putInCustomData(self::CUSTOM_DATA_OVERRIDE_MANUAL_EDITS, $v);}
// 	public function setUrgentReference($v)		{$this->putInCustomData(self::CUSTOM_DATA_URGENT_REFERENCE, $v);}
// 	public function setAllowSyndication($v)		{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_SYNDICATION, $v);}
// 	public function setHideViewCount($v)		{$this->putInCustomData(self::CUSTOM_DATA_HIDE_VIEW_COUNT, $v);}
// 	public function setAllowAdsenseForVideo($v)	{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_ADSENSE_FOR_VIDEO, $v);}
// 	public function setAllowInvideo($v)			{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_INVIDEO, $v);}
// 	public function setAllowMidRollAds($v)		{$this->putInCustomData(self::CUSTOM_DATA_ALLOW_MID_ROLL_ADS, $v);}
// 	public function setInstreamStandard($v)		{$this->putInCustomData(self::CUSTOM_DATA_INSTREAM_STANDARD, $v);}
// 	public function setInstreamTrueview($v)		{$this->putInCustomData(self::CUSTOM_DATA_INSTREAM_TRUEVIEW, $v);}
// 	public function setClaimType($v)			{$this->putInCustomData(self::CUSTOM_DATA_CLAIM_TYPE, $v);}
// 	public function setBlockOutsideOwnership($v){$this->putInCustomData(self::CUSTOM_DATA_BLOCK_OUTSIDE_OWNERSHIP, $v);}
// 	public function setCaptionAutosync($v)		{$this->putInCustomData(self::CUSTOM_DATA_CAPTION_AUTOSYNC, $v);}
// 	public function setDeleteReference($v)		{$this->putInCustomData(self::CUSTOM_DATA_DELETE_REFERENCE, $v);}
// 	public function setReleaseClaims($v)		{$this->putInCustomData(self::CUSTOM_DATA_RELEASE_CLAIMS, $v);}
    
	
	protected function getDefaultFieldConfigArray()
	{
	    $fieldConfigArray = parent::getDefaultFieldConfigArray();
	      
	    // media fields
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(TvinciDistributionField::MEDIA_TITLE);
	    $fieldConfig->setUserFriendlyFieldName('Entry name');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::NAME));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(TvinciDistributionField::MEDIA_DESCRIPTION);
	    $fieldConfig->setUserFriendlyFieldName('Entry description');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
	    $fieldConfig->setUpdateOnChange(true);
	    $fieldConfig->setUpdateParams(array(entryPeer::DESCRIPTION));
	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

	    // Set the default XSL expression for AUTOMATIC_DISTRIBUTION_CONDITIONS
	    $fieldConfig = $fieldConfigArray[ConfigurableDistributionField::AUTOMATIC_DISTRIBUTION_CONDITIONS];
	    if ( $fieldConfig )
	    {
			$fieldConfig->setEntryMrssXslt('<xsl:if test="customData/metadata/WorkflowStatus = \'Approved\'">Approved For Automatic Distribution</xsl:if>');
	    }

	    $activatePublishingXSLT = '<xsl:choose>'
	    							. '<xsl:when test="customData/metadata/Activate = \'Yes\'">true</xsl:when>'
	    							. '<xsl:otherwise>false</xsl:otherwise>'
	    						. '</xsl:choose>';
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ACTIVATE_PUBLISHING, 'Activate Publishing', 'Activate', false, DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER, $activatePublishingXSLT);

	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::MEDIA_TYPE, 'Media Type', 'MediaType');

	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::GEO_BLOCK_RULE, 'Geo Block Rule', 'GeoBlockRule');
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::WATCH_PERMISSIONS_RULE, 'Watch Permission Rule', 'WatchPermissionRule');

	    // Language
	    $languageXSLT = '<xsl:choose>'
	    					. '<xsl:when test="customData/metadata/Language != \'\'">'
		    					. '<xsl:value-of select="customData/metadata/Language"/>'
		    				. '</xsl:when>'
	    					. '<xsl:otherwise><xsl:text>eng</xsl:text></xsl:otherwise>'
	    				.'</xsl:choose>'
	    			;
		$languageUpdateParamsArray = array( "/*[local-name()='metadata']/*[local-name()='Language']" );
	    $this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::LANGUAGE, 'Language', $languageXSLT, false, true, $languageUpdateParamsArray);

	    // Dates
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::START_DATE, 'Start Date', 'StartDate');
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::END_DATE, 'End Date', 'FinalEndDate');
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::CATALOG_START_DATE, 'Catalog Start Date', 'CatalogStartDate');
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::CATALOG_END_DATE, 'Catalog End Date', 'CatalogEndDate');
	    	    
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_RUNTIME, 'Runtime', 'Runtime');
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_RELEASE_YEAR, 'Release Year', 'ReleaseYear');
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_RELEASE_DATE, 'Release Date', 'ReleaseDate');
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_GENRE, 'Genre', 'Genre', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_SUB_GENRE, 'Sub Genre', 'SubGenre', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_RATING, 'Rating', 'Rating', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_COUNTRY, 'Country', 'Country', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_CAST, 'Cast', 'Cast', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_DIRECTOR, 'Director', 'Director', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_AUDIO_LANGUAGE, 'Audio Language', 'AudioLanguage', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_STUDIO, 'Studio', 'Studio', true);
 	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::METADATA_STUDIO, 'Studio', 'Studio', true);
	    
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::VIDEO_ASSET_MAIN, 'Main Video Asset', 'MainVideoAsset');
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::VIDEO_ASSET_TABLET_MAIN, 'Tablet Video Asset', 'TabletMainVideoAsset');
	    $this->addMetadataDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::VIDEO_ASSET_SMARTPHONE_MAIN, 'Smartphone Video Asset', 'SmartphoneMainVideoAsset');
 	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::MEDIA_KEYWORDS);
// 	    $fieldConfig->setUserFriendlyFieldName('Entry tags');
// 	    $fieldConfig->setEntryMrssXslt(
// 	    			'<xsl:for-each select="tags/tag">
// 						<xsl:if test="position() &gt; 1">
// 							<xsl:text>,</xsl:text>
// 						</xsl:if>
// 						<xsl:value-of select="." />
// 					</xsl:for-each>');
// 	    $fieldConfig->setUpdateOnChange(true);
// 	    $fieldConfig->setUpdateParams(array(entryPeer::TAGS));
// 	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::MEDIA_RATING);
// 	    $fieldConfig->setUserFriendlyFieldName('Media rating');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::MEDIA_CATEGORY);
// 	    $fieldConfig->setUserFriendlyFieldName('Media category');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/default_category" />');
// 	    $fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::WEB_METADATA_CUSTOM_ID);
// 	    $fieldConfig->setUserFriendlyFieldName('Entry ID');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::WEB_METADATA_NOTES);
// 	    $fieldConfig->setUserFriendlyFieldName('Web metadata notes');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::MOVIE_METADATA_CUSTOM_ID);
// 	    $fieldConfig->setUserFriendlyFieldName('Movie metadata custom ID');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::MOVIE_METADATA_DIRECTOR);
// 	    $fieldConfig->setUserFriendlyFieldName('Movie metadata director');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::MOVIE_METADATA_NOTES);
// 	    $fieldConfig->setUserFriendlyFieldName('Movie metadata notes');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::MOVIE_METADATA_TITLE);
// 	    $fieldConfig->setUserFriendlyFieldName('Movie metadata title');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::MOVIE_METADATA_TMS_ID);
// 	    $fieldConfig->setUserFriendlyFieldName('Movie metadata TMS ID');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::DATE_RECORDED);
// 	    $fieldConfig->setUserFriendlyFieldName('Date recorded');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
//         $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::START_TIME);
// 	    $fieldConfig->setUserFriendlyFieldName('Distribution sunrise');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunrise" />');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::END_TIME);
// 	    $fieldConfig->setUserFriendlyFieldName('Distribution sunset');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunset" />');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::URGENT_REFERENCE_FILE);
// 	    $fieldConfig->setUserFriendlyFieldName('Urgent reference file');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::KEEP_FINGERPRINT);
// 	    $fieldConfig->setUserFriendlyFieldName('Keep fingerprint');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    // community fields
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::ALLOW_COMMENTS);
// 	    $fieldConfig->setUserFriendlyFieldName('Allow comments');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_comments" />');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::ALLOW_RESPONSES);
// 	    $fieldConfig->setUserFriendlyFieldName('Allow responses');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_responses" />');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::ALLOW_RATINGS);
// 	    $fieldConfig->setUserFriendlyFieldName('Allow ratings');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_ratings" />');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::ALLOW_EMBEDDING);
// 	    $fieldConfig->setUserFriendlyFieldName('Allow embedding');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_embedding" />');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    // youtube extra data
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::POLICY_COMMERCIAL);
// 	    $fieldConfig->setUserFriendlyFieldName('Commercial policy');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/commerical_policy" />');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::POLICY_UGC);
// 	    $fieldConfig->setUserFriendlyFieldName('UGC policy');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/ugc_policy" />');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::NOTIFICATION_EMAIL);
// 	    $fieldConfig->setUserFriendlyFieldName('Notification Email');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/notification_email" />');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::ACCOUNT_USERNAME);
// 	    $fieldConfig->setUserFriendlyFieldName('Account username');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/account_username" />');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::ACCOUNT_PASSWORD);
// 	    $fieldConfig->setUserFriendlyFieldName('Account password');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>'); // the password should not be added in contributeMRSS
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::OWNER_NAME);
// 	    $fieldConfig->setUserFriendlyFieldName('Account username');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/account_username" />');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::TARGET);
// 	    $fieldConfig->setUserFriendlyFieldName('Tvinci target');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/target" />');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::LANGUAGE);
// 	    $fieldConfig->setUserFriendlyFieldName('Tvinci language');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text>en</xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::TV_METADATA_CUSTOM_ID);
// 	    $fieldConfig->setUserFriendlyFieldName('TV metadata custom id');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::TV_METADATA_EPISODE);
// 	    $fieldConfig->setUserFriendlyFieldName('TV metadata episode');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::TV_METADATA_EPISODE_TITLE);
// 	    $fieldConfig->setUserFriendlyFieldName('TV metadata episode title');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::TV_METADATA_SHOW_TITLE);
// 	    $fieldConfig->setUserFriendlyFieldName('TV metadata show title');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::TV_METADATA_SEASON);
// 	    $fieldConfig->setUserFriendlyFieldName('TV metadata season');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::TV_METADATA_NOTES);
// 	    $fieldConfig->setUserFriendlyFieldName('TV metadata notes');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::TV_METADATA_TMS_ID);
// 	    $fieldConfig->setUserFriendlyFieldName('TV metadata TMS ID');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::PLAYLISTS);
// 	    $fieldConfig->setUserFriendlyFieldName('Tvinci playlists');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TvinciPlaylist" />');
// 	    $fieldConfig->setUpdateOnChange(true);
// 	    $fieldConfig->setUpdateParams(array("/*[local-name()='metadata']/*[local-name()='TvinciPlaylist']"));
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::ADVERTISING_ADSENSE_FOR_VIDEO);
// 	    $fieldConfig->setUserFriendlyFieldName('Advertising adsense for video');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::ADVERTISING_INVIDEO);
// 	    $fieldConfig->setUserFriendlyFieldName('Advertising in video');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::THIRD_PARTY_AD_SERVER_AD_TYPE);
// 	    $fieldConfig->setUserFriendlyFieldName('Third party ad server ad type');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text>1</xsl:text>');
// 	    $fieldConfig->setUpdateOnChange(false);
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::THIRD_PARTY_AD_SERVER_PARTNER_ID);
// 	    $fieldConfig->setUserFriendlyFieldName('Third party ad server partner ID');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/ad_server_partner_id" />');
// 	    $fieldConfig->setUpdateOnChange(false);
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::THIRD_PARTY_AD_SERVER_VIDEO_ID);
// 	    $fieldConfig->setUserFriendlyFieldName('Entry ID');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(entryId)" />');
// 	    $fieldConfig->setUpdateOnChange(false);
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::ADVERTISING_ALLOW_PRE_ROLL_ADS);
// 	    $fieldConfig->setUserFriendlyFieldName('Allow Pre Roll Ads');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_pre_roll_ads" />');
// 	    $fieldConfig->setUpdateOnChange(false);
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

// 		$fieldConfig = new DistributionFieldConfig();
// 		$fieldConfig->setFieldName(TvinciDistributionField::ADVERTISING_ALLOW_MID_ROLL_ADS);
// 		$fieldConfig->setUserFriendlyFieldName('Allow Mid Roll Ads');
// 		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_mid_roll_ads" />');
// 		$fieldConfig->setUpdateOnChange(false);
// 		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::ADVERTISING_ALLOW_POST_ROLL_ADS);
// 	    $fieldConfig->setUserFriendlyFieldName('Allow Post Roll Ads');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/allow_post_roll_ads" />');
// 	    $fieldConfig->setUpdateOnChange(false);
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::LOCATION_COUNTRY);
// 	    $fieldConfig->setUserFriendlyFieldName('Location country');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::LOCATION_LOCATION_TEXT);
// 	    $fieldConfig->setUserFriendlyFieldName('Location text');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::LOCATION_ZIP_CODE);
// 	    $fieldConfig->setUserFriendlyFieldName('Location zip code');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;
	    
// 	    $fieldConfig = new DistributionFieldConfig();
// 	    $fieldConfig->setFieldName(TvinciDistributionField::DISTRIBUTION_RESTRICTION_DISTRIBUTION_RULE);
// 	    $fieldConfig->setUserFriendlyFieldName('Distribution restriction rule');
// 	    $fieldConfig->setEntryMrssXslt('<xsl:text></xsl:text>');
// 	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_ACTOR, 'Asset actor', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_BROADCASTER, 'Asset broadcaster', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_CONTENT_TYPE, 'Asset content type', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_CUSTOM_ID, 'Asset custom id', '<xsl:value-of select="string(entryId)" />');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_DESCRIPTION, 'Asset description', '<xsl:value-of select="string(description)" />');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_DIRECTOR, 'Asset director', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_EIDR, 'Asset EIDR', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_END_YEAR, 'Asset end year', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_EPISODE, 'Asset episode', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_GENRE, 'Asset genre', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_GRID, 'Asset GRid', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_ISAN, 'Asset ISAN', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_KEYWORDS, 'Asset keywords', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_NOTES, 'Asset notes', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_ORIGINAL_RELEASE_DATE, 'Asset original release date', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_ORIGINAL_RELEASE_MEDIUM, 'Asset original medium', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_PRODUCER, 'Asset producer', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_RATING_SYSTEM, 'Asset rating system', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_RATING_VALUE, 'Asset rating value', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_SEASON, 'Asset season', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_SHOW_AND_MOVIE_PROGRAMMING, 'Asset show and movie programming', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_SHOW_TITLE, 'Asset show title', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_SPOKEN_LANGUAGE, 'Asset spoken language', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_START_YEAR, 'Asset start year', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_SUBTITLED_LANGUAGE, 'Asset subtitles language', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_TITLE, 'Asset title', '<xsl:value-of select="string(title)" />');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_TYPE, 'Asset type', '<xsl:text>web</xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_TMS_ID, 'Asset TMS ID', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_UPC, 'Asset UPC', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_URL, 'Asset URL', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ASSET_WRITER, 'Asset Writer', '<xsl:text></xsl:text>');

// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::VIDEO_ALLOW_COMMENT_RATINGS, 'Video allow comment ratings', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::VIDEO_ALLOW_SYNDICATION, 'Video allow syndication', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::VIDEO_CHANNEL, 'Video channel', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::VIDEO_HIDE_VIEW_COUNT, 'Video hide view count', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::VIDEO_DOMAIN_BLACK_LIST, 'Video domain black list', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::VIDEO_DOMAIN_WHITE_LIST, 'Video domain white list', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::VIDEO_NOTIFY_SUBSCRIBERS, 'Video notify subscribers', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::VIDEO_PUBLIC, 'Video public', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::VIDEO_CHANNEL, 'Video channel', '<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/account_username" />');

// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::CLAIM_TYPE, 'Claim type', '<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/claim_type" />');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::CLAIM_BLOCK_OUTSIDE_OWNERSHIP, 'Video block outside ownership', '<xsl:text></xsl:text>');
// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::ADVERTISING_INSTREAM_STANDARD, 'Instream standard', '<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/instream_standard" />');

// 		$this->addDistributionFieldConfig($fieldConfigArray, TvinciDistributionField::DISABLE_FINGERPRINTING, 'Disable fingerprinting/claiming', '<xsl:text></xsl:text>');

// 		if ($this->getFeedSpecVersion() == TvinciDistributionFeedSpecVersion::VERSION_2)
// 			$this->removeDistributionFieldConfigs($fieldConfigArray, $this->specV1OnlyFields);
// 		else
// 			$this->removeDistributionFieldConfigs($fieldConfigArray, $this->specV2OnlyFields);


	    return $fieldConfigArray;
	}
	
	protected function addMetadataDistributionFieldConfig(array &$array, $name, $friendlyName, $metadataName, $multiValue = false, $required = DistributionFieldRequiredStatus::NOT_REQUIRED, $xslt = null)
	{
		$metadataPath = "customData/metadata/$metadataName";
		if ( is_null($xslt) )
		{
			if ( ! $multiValue ) // Single value
			{
				$xslt = '<xsl:value-of select="string('. $metadataPath . ')" />';
			}
			else
			{
				$xslt = '<xsl:for-each select="'. $metadataPath . '">'
							. '<xsl:if test="position() &gt; 1">'
							. '<xsl:text>,</xsl:text>'
							. '</xsl:if>'
							. '<xsl:value-of select="string(.)" />'
						. '</xsl:for-each>'
					;
			}
		}
		
		$updateMetadataArray = array( "/*[local-name()='metadata']/*[local-name()='$metadataName']" );
		
		$this->addDistributionFieldConfig($array, $name, $friendlyName, $xslt, $required, true, $updateMetadataArray);
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
	
	public function DEBUG_PLEASE_REMOVE_getFieldsAsArray($valuesXmlStr) {
// 	    echo('All field values result XML: '.$valuesXmlStr);
	    
		$valuesXmlObj = new DOMDocument();
		$valuesXmlObj->loadXML($valuesXmlStr);
	    
	    $fieldValues = array();
	    $fieldConfigArray = $this->getFieldConfigArray();
	    foreach ($fieldConfigArray as $fieldConfig)
	    {
	        $fieldName = $fieldConfig->getFieldName();
	        $fieldValues[$fieldName] = $this->getFieldValueFromXml($fieldName, $valuesXmlObj);
	        if ( is_null( $fieldValues[$fieldName] ) ) {
	        	echo "$fieldName is null\n";
	        }
	    }
	    
	    return $fieldValues;
	}
}