<?php


/**
 * @package plugins.facebookDistribution
 * @subpackage model
 */
class FacebookDistributionProfile extends ConfigurableDistributionProfile
{
	const CUSTOM_DATA_PAGE_ID = 'pageId';
	const CUSTOM_DATA_PAGE_ACCESS_TOKEN = 'pageAccessToken';
	const CUSTOM_DATA_USER_ACCESS_TOKEN = 'userAccessToken';
	// state is the keyword that facebook uses to save a session token
	const CUSTOM_DATA_STATE = 'state';
	const CUSTOM_DATA_FACEBOOK_PERMISSIONS = 'facebookPermissions';
	const CUSTOM_DATA_RE_REQUEST_PERMISSIONS = 'reRequestPermissions';
	const CUSTOM_DATA_CALL_TO_ACTION_TYPE = 'callToActionType';
	const CUSTOM_DATA_CALL_TO_ACTION_LINK = 'callToActionLink';
	const CUSTOM_DATA_CALL_TO_ACTION_LINK_CAPTION = 'callToActionLinkCaption';
	const CUSTOM_DATA_PLACE= 'place';
	const CUSTOM_DATA_TAGS= 'tags';
	const CUSTOM_DATA_FEED_TARGETING= 'feedTargeting';
	// this list is the available one when uploading a video to a page
	const CALL_TO_ACTION_TYPE_VALID_VALUES = 'SHOP_NOW,BOOK_TRAVEL,LEARN_MORE,SIGN_UP,DOWNLOAD,WATCH_MORE';
	const DEFAULT_RE_REQUEST_PERMISSIONS = 'false';
	// needed permission in order to be able to publish the video to a facebook page
	const DEFAULT_FACEBOOK_PERMISSIONS = 'pages_manage_posts';
	// targeting
	const CUSTOM_DATA_TARGETING_COUNTRIES = 'targetingCountries'; //list
	const CUSTOM_DATA_TARGETING_REGIONS = 'targetingRegions'; //list
	const CUSTOM_DATA_TARGETING_CITIES = 'targetingCities'; //list
	const CUSTOM_DATA_TARGETING_ZIP_CODES = 'targetingZipCodes'; //list
	const CUSTOM_DATA_TARGETING_EXCLUDED_COUNTRIES = 'targetingExcludedCountries';//list
	const CUSTOM_DATA_TARGETING_EXCLUDED_REGIONS = 'targetingExcludedRegions';//list
	const CUSTOM_DATA_TARGETING_EXCLUDED_CITIES = 'targetingExcludedCities';//list
	const CUSTOM_DATA_TARGETING_EXCLUDED_ZIPCODES = 'targetingExcludedZipCodes';//list
	const CUSTOM_DATA_TARGETING_TIMEZONES = 'targetingTimezones';//list
	const CUSTOM_DATA_TARGETING_AGE_MIN = 'targetingAgeMin';
	const CUSTOM_DATA_TARGETING_AGE_MAX = 'targetingAgeMax';
	const CUSTOM_DATA_TARGETING_GENDERS = 'targetingGenders';//list
	const CUSTOM_DATA_TARGETING_LOCALES = 'targetingLocales';//list

	const CUSTOM_DATA_FEED_TARGETING_COUNTRIES = 'feedTargetingCountries'; //list
	const CUSTOM_DATA_FEED_TARGETING_REGIONS = 'feedTargetingRegions'; //list
	const CUSTOM_DATA_FEED_TARGETING_CITIES = 'feedTargetingCities'; //list
	const CUSTOM_DATA_FEED_TARGETING_AGE_MIN = 'feedTargetingAgeMin';
	const CUSTOM_DATA_FEED_TARGETING_AGE_MAX = 'feedTargetingAgeMax';
	const CUSTOM_DATA_FEED_TARGETING_GENDERS = 'feedTargetingGenders';//list
	const CUSTOM_DATA_FEED_TARGETING_LOCALES = 'feedTargetingLocales';//list
	const CUSTOM_DATA_FEED_TARGETING_RELATIONSHIP_STATUSES = 'feedTargetingRelationshipStatuses';//list
	const CUSTOM_DATA_FEED_TARGETING_INTERESTED_IN = 'feedTargetingInterestedIn';//list
	const CUSTOM_DATA_FEED_TARGETING_EDUCATION_STATUSES = 'feedTargetingEducationStatuses';//list
	const CUSTOM_DATA_FEED_TARGETING_COLLEGE_YEARS = 'feedTargetingCollegeYears';//list
	const CUSTOM_DATA_FEED_TARGETING_INTERESTS = 'feedTargetingInterests';//list
	const CUSTOM_DATA_FEED_TARGETING_RELEVANT_UNTIL = 'feedTargetingRelevantUntil';



	/* (non-PHPdoc)
	 * @see DistributionProfile::getProvider()
	 */
	public function getProvider()
	{
		return FacebookDistributionPlugin::getProvider();
	}

	/* (non-PHPdoc)
	 * @see DistributionProfile::validateForSubmission()
	 */
	public function validateForSubmission(EntryDistribution $entryDistribution, $action)
	{
		$validationErrors = parent::validateForSubmission($entryDistribution, $action);

		$inListOrNullFields = array (
			FacebookDistributionField::CALL_TO_ACTION_TYPE_VALID_VALUES => explode(',', self::CALL_TO_ACTION_TYPE_VALID_VALUES),
		);

		if(count($entryDistribution->getFlavorAssetIds()))
			$flavorAssets = assetPeer::retrieveByIds(explode(',', $entryDistribution->getFlavorAssetIds()));
		else
			$flavorAssets = assetPeer::retrieveReadyFlavorsByEntryId($entryDistribution->getEntryId());

		$validVideo = false;
		foreach ($flavorAssets as $flavorAsset)
		{
			$validVideo = $this->validateVideo($flavorAsset);
			if($validVideo) {
				// even one valid video is enough
				break;
			}
		}

		if(!$validVideo)
		{
			KalturaLog::err("No valid video found for entry [" . $entryDistribution->getEntryId() . "]");
			$validationErrors[] = $this->createCustomValidationError($action, DistributionErrorType::INVALID_DATA, 'flavorAsset', ' No valid flavor found');
		}

		$allFieldValues = $this->getAllFieldValues($entryDistribution);
		if (!$allFieldValues || !is_array($allFieldValues)) {
			KalturaLog::err('Error getting field values from entry distribution id ['.$entryDistribution->getId().'] profile id ['.$this->getId().']');
			return $validationErrors;
		}
		if ($allFieldValues[FacebookDistributionField::SCHEDULE_PUBLISHING_TIME] &&
			$allFieldValues[FacebookDistributionField::SCHEDULE_PUBLISHING_TIME] > time() &&
			!dateUtils::isWithinTimeFrame($allFieldValues[FacebookDistributionField::SCHEDULE_PUBLISHING_TIME],
				FacebookConstants::FACEBOOK_MIN_POSTPONE_POST_IN_SECONDS,
				FacebookConstants::FACEBOOK_MAX_POSTPONE_POST_IN_SECONDS))
		{
			KalturaLog::err("Scheduled time to publish defies the facebook restriction of six minute to six months from now got".$allFieldValues[FacebookDistributionField::SCHEDULE_PUBLISHING_TIME]);
			$validationErrors[] = $this->createCustomValidationError($action, DistributionErrorType::INVALID_DATA, 'sunrise', 'Distribution sunrise is invalid (should be 10 minutes to 6 months from now)');
		}
		$validationErrors = array_merge($validationErrors, $this->validateInListOrNull($inListOrNullFields, $allFieldValues, $action));
		return $validationErrors;
	}

	public function getPageId()						{return $this->getFromCustomData(self::CUSTOM_DATA_PAGE_ID);}
	public function getPageAccessToken()			{return $this->getFromCustomData(self::CUSTOM_DATA_PAGE_ACCESS_TOKEN);}
	public function getUserAccessToken()			{return $this->getFromCustomData(self::CUSTOM_DATA_USER_ACCESS_TOKEN);}
	public function getState()						{return $this->getFromCustomData(self::CUSTOM_DATA_STATE);}
	public function getFacebookPermissions()		{return $this->getFromCustomData(self::CUSTOM_DATA_FACEBOOK_PERMISSIONS, null, self::DEFAULT_FACEBOOK_PERMISSIONS);}
	public function getReRequestPermissions()		{return $this->getFromCustomData(self::CUSTOM_DATA_RE_REQUEST_PERMISSIONS, null , self::DEFAULT_RE_REQUEST_PERMISSIONS);}
	public function getCallToActionType()			{return $this->getFromCustomData(self::CUSTOM_DATA_CALL_TO_ACTION_TYPE);}
	public function getCallToActionLink()			{return $this->getFromCustomData(self::CUSTOM_DATA_CALL_TO_ACTION_LINK);}
	public function getCallToActionLinkCaption()	{return $this->getFromCustomData(self::CUSTOM_DATA_CALL_TO_ACTION_LINK_CAPTION);}
	public function getPlace()						{return $this->getFromCustomData(self::CUSTOM_DATA_PLACE);}
	public function getTags()						{return $this->getFromCustomData(self::CUSTOM_DATA_TAGS);}
	public function getFeedTargeting()				{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TARGETING);}

	public function setPageId($v)					{$this->putInCustomData(self::CUSTOM_DATA_PAGE_ID, $v);}
	public function setPageAccessToken($v)			{$this->putInCustomData(self::CUSTOM_DATA_PAGE_ACCESS_TOKEN, $v);}
	public function setUserAccessToken($v)			{$this->putInCustomData(self::CUSTOM_DATA_USER_ACCESS_TOKEN, $v);}
	public function setState($v)					{$this->putInCustomData(self::CUSTOM_DATA_STATE, $v);}
	public function setFacebookPermissions($v)		{$this->putInCustomData(self::CUSTOM_DATA_FACEBOOK_PERMISSIONS, $v);}
	public function setReRequestPermissions($v)		{$this->putInCustomData(self::CUSTOM_DATA_RE_REQUEST_PERMISSIONS, $v);}
	public function setCallToActionType($v)			{$this->putInCustomData(self::CUSTOM_DATA_CALL_TO_ACTION_TYPE, $v);}
	public function setCallToActionLink($v)			{$this->putInCustomData(self::CUSTOM_DATA_CALL_TO_ACTION_LINK, $v);}
	public function setCallToActionLinkCaption($v)	{$this->putInCustomData(self::CUSTOM_DATA_CALL_TO_ACTION_LINK_CAPTION, $v);}
	public function setPlace($v)					{$this->putInCustomData(self::CUSTOM_DATA_PLACE, $v);}
	public function setTags($v)						{$this->putInCustomData(self::CUSTOM_DATA_TAGS, $v);}
	public function setFeedTargeting($v)			{$this->putInCustomData(self::CUSTOM_DATA_FEED_TARGETING, $v);}

	public function getTargetingCountries()			{return $this->getFromCustomData(self::CUSTOM_DATA_TARGETING_COUNTRIES);}
	public function getTargetingRegions()			{return $this->getFromCustomData(self::CUSTOM_DATA_TARGETING_REGIONS );}
	public function getTargetingCities()			{return $this->getFromCustomData(self::CUSTOM_DATA_TARGETING_CITIES);}
	public function getTargetingZipCodes()			{return $this->getFromCustomData(self::CUSTOM_DATA_TARGETING_ZIP_CODES);}
	public function getTargetingExcludedCountries()	{return $this->getFromCustomData(self::CUSTOM_DATA_TARGETING_EXCLUDED_COUNTRIES);}
	public function getTargetingExcludedRegions()	{return $this->getFromCustomData(self::CUSTOM_DATA_TARGETING_EXCLUDED_REGIONS);}
	public function getTargetingExcludedCities()	{return $this->getFromCustomData(self::CUSTOM_DATA_TARGETING_EXCLUDED_CITIES);}
	public function getTargetingExcludedZipCodes()	{return $this->getFromCustomData(self::CUSTOM_DATA_TARGETING_EXCLUDED_ZIPCODES);}
	public function getTargetingTimezones()			{return $this->getFromCustomData(self::CUSTOM_DATA_TARGETING_TIMEZONES);}
	public function getTargetingAgeMax()			{return $this->getFromCustomData(self::CUSTOM_DATA_TARGETING_AGE_MAX);}
	public function getTargetingAgeMin()			{return $this->getFromCustomData(self::CUSTOM_DATA_TARGETING_AGE_MIN);}
	public function getTargetingGenders()			{return $this->getFromCustomData(self::CUSTOM_DATA_TARGETING_GENDERS);}
	public function getTargetingLocales()			{return $this->getFromCustomData(self::CUSTOM_DATA_TARGETING_LOCALES);}

	public function setTargetingCountries($v)			{$this->putInCustomData(self::CUSTOM_DATA_TARGETING_COUNTRIES, $v);}
	public function setTargetingRegions($v)				{$this->putInCustomData(self::CUSTOM_DATA_TARGETING_REGIONS, $v);}
	public function setTargetingCities($v)				{$this->putInCustomData(self::CUSTOM_DATA_TARGETING_CITIES, $v);}
	public function setTargetingZipCodes($v)			{$this->putInCustomData(self::CUSTOM_DATA_TARGETING_ZIP_CODES, $v);}
	public function setTargetingExcludedCountries($v)	{$this->putInCustomData(self::CUSTOM_DATA_TARGETING_EXCLUDED_COUNTRIES, $v);}
	public function setTargetingExcludedRegions($v)		{$this->putInCustomData(self::CUSTOM_DATA_TARGETING_EXCLUDED_REGIONS, $v);}
	public function setTargetingExcludedCities($v)		{$this->putInCustomData(self::CUSTOM_DATA_TARGETING_EXCLUDED_CITIES, $v);}
	public function setTargetingExcludedZipCodes($v)	{$this->putInCustomData(self::CUSTOM_DATA_TARGETING_EXCLUDED_ZIPCODES, $v);}
	public function setTargetingTimezones($v)			{$this->putInCustomData(self::CUSTOM_DATA_TARGETING_TIMEZONES, $v);}
	public function setTargetingAgeMax($v)				{$this->putInCustomData(self::CUSTOM_DATA_TARGETING_AGE_MAX, $v);}
	public function setTargetingAgeMin($v)				{$this->putInCustomData(self::CUSTOM_DATA_TARGETING_AGE_MIN, $v);}
	public function setTargetingGenders($v)				{$this->putInCustomData(self::CUSTOM_DATA_TARGETING_GENDERS, $v);}
	public function setTargetingLocales($v)				{$this->putInCustomData(self::CUSTOM_DATA_TARGETING_LOCALES, $v);}

	public function getFeedTargetingCountries()				{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TARGETING_COUNTRIES);}
	public function getFeedTargetingRegions()				{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TARGETING_REGIONS );}
	public function getFeedTargetingCities()				{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TARGETING_CITIES);}
	public function getFeedTargetingAgeMax()				{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TARGETING_AGE_MAX);}
	public function getFeedTargetingAgeMin()				{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TARGETING_AGE_MIN);}
	public function getFeedTargetingGenders()				{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TARGETING_GENDERS);}
	public function getFeedTargetingLocales()				{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TARGETING_LOCALES);}
	public function getFeedTargetingRelationshipStatuses()	{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TARGETING_RELATIONSHIP_STATUSES);}
	public function getFeedTargetingInterestedIn()			{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TARGETING_INTERESTED_IN);}
	public function getFeedTargetingEducationalStatuses()	{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TARGETING_EDUCATION_STATUSES);}
	public function getFeedTargetingCollegeYears()			{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TARGETING_COLLEGE_YEARS);}
	public function getFeedTargetingInterests()				{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TARGETING_INTERESTS);}
	public function getFeedTargetingRelevantUntil()			{return $this->getFromCustomData(self::CUSTOM_DATA_FEED_TARGETING_RELEVANT_UNTIL);}

	public function setFeedTargetingCountries($v)			{$this->putInCustomData(self::CUSTOM_DATA_FEED_TARGETING_COUNTRIES, $v);}
	public function setFeedTargetingRegions($v)				{$this->putInCustomData(self::CUSTOM_DATA_FEED_TARGETING_REGIONS, $v);}
	public function setFeedTargetingCities($v)				{$this->putInCustomData(self::CUSTOM_DATA_FEED_TARGETING_CITIES, $v);}
	public function setFeedTargetingAgeMax($v)				{$this->putInCustomData(self::CUSTOM_DATA_FEED_TARGETING_AGE_MAX, $v);}
	public function setFeedTargetingAgeMin($v)				{$this->putInCustomData(self::CUSTOM_DATA_FEED_TARGETING_AGE_MIN, $v);}
	public function setFeedTargetingGenders($v)				{$this->putInCustomData(self::CUSTOM_DATA_FEED_TARGETING_GENDERS, $v);}
	public function setFeedTargetingLocales($v)				{$this->putInCustomData(self::CUSTOM_DATA_FEED_TARGETING_LOCALES, $v);}
	public function setFeedTargetingRelationshipStatuses($v){$this->putInCustomData(self::CUSTOM_DATA_FEED_TARGETING_RELATIONSHIP_STATUSES, $v);}
	public function setFeedTargetingInterestedIn($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_TARGETING_INTERESTED_IN, $v);}
	public function setFeedTargetingEducationalStatuses($v)	{$this->putInCustomData(self::CUSTOM_DATA_FEED_TARGETING_EDUCATION_STATUSES, $v);}
	public function setFeedTargetingCollegeYears($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_TARGETING_COLLEGE_YEARS, $v);}
	public function setFeedTargetingInterests($v)			{$this->putInCustomData(self::CUSTOM_DATA_FEED_TARGETING_INTERESTS, $v);}
	public function setFeedTargetingRelevantUntil($v)		{$this->putInCustomData(self::CUSTOM_DATA_FEED_TARGETING_RELEVANT_UNTIL, $v);}


	protected function getDefaultFieldConfigArray()
	{
		$fieldConfigArray = parent::getDefaultFieldConfigArray();

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::TITLE);
		$fieldConfig->setUserFriendlyFieldName('Video title');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(title)" />');
		$fieldConfig->setUpdateOnChange(true);
		$fieldConfig->setUpdateParams(array(entryPeer::NAME));
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::DESCRIPTION);
		$fieldConfig->setUserFriendlyFieldName('Video description');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="string(description)" />');
		$fieldConfig->setUpdateOnChange(true);
		$fieldConfig->setUpdateParams(array(entryPeer::DESCRIPTION));
		$fieldConfig->setIsRequired(DistributionFieldRequiredStatus::REQUIRED_BY_PROVIDER);
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::SCHEDULE_PUBLISHING_TIME);
		$fieldConfig->setUserFriendlyFieldName('Schedule Sunrise Time');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="distribution[@entryDistributionId=$entryDistributionId]/sunrise" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::CALL_TO_ACTION_TYPE);
		$fieldConfig->setUserFriendlyFieldName('Call To Action Type');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/CallToActionType" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::CALL_TO_ACTION_LINK);
		$fieldConfig->setUserFriendlyFieldName('Call To Action Link');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/CallToActionLink" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::CALL_TO_ACTION_LINK_CAPTION);
		$fieldConfig->setUserFriendlyFieldName('Call To Action Link Caption');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/CallToActionLinkCaption" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::TARGETING_COUNTRIES);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TargetingCountries" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::TARGETING_CITIES);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TargetingCities" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::TARGETING_REGIONS);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TargetingRegions" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::TARGETING_ZIP_CODES);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TargetingZipCodes" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::TARGETING_EXCLUDED_COUNTRIES);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TargetingExcludedCountries" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::TARGETING_EXCLUDED_CITIES);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TargetingExcludedCities" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::TARGETING_EXCLUDED_REGIONS);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TargetingExcludedRegions" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::TARGETING_EXCLUDED_ZIPCODES);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TargetingExcludedZipCodes" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::TARGETING_TIMEZONES);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TargetingTimezones" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::TARGETING_AGE_MAX);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TargetingAgeMax" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::TARGETING_AGE_MIN);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TargetingAgeMin" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::TARGETING_GENDERS);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TargetingGenders" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::TARGETING_LOCALES);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/TargetingLocale" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::FEED_TARGETING_COUNTRIES);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/FeedTargetingCountries" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::FEED_TARGETING_CITIES);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/FeedTargetingCities" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::FEED_TARGETING_REGIONS);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/FeedTargetingRegions" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::FEED_TARGETING_AGE_MAX);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/FeedTargetingAgeMax" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::FEED_TARGETING_AGE_MIN);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/FeedTargetingAgeMin" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::FEED_TARGETING_GENDERS);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/FeedTargetingGenders" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::FEED_TARGETING_INTERESTED_IN);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/FeedTargetingInterestedIn" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::FEED_TARGETING_EDUCATION_STATUSES);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/FeedTargetingEducationalStatuses" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::FEED_TARGETING_RELATIONSHIP_STATUSES);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/FeedTargetingRelationshipStatuses" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::FEED_TARGETING_COLLEGE_YEARS);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/FeedTargetingCollegeYears" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::FEED_TARGETING_INTERESTS);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/FeedTargetingInterests" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::FEED_TARGETING_RELEVANT_UNTIL);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/FeedTargetingRelevantUntil" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::FEED_TARGETING_LOCALES);
		$fieldConfig->setUserFriendlyFieldName('Key IDs for ad targeting objects used to limit the audience of the video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/FeedTargetingLocale" />');
		$fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		$fieldConfig = new DistributionFieldConfig();
		$fieldConfig->setFieldName(FacebookDistributionField::PLACE);
		$fieldConfig->setUserFriendlyFieldName('ID of location to tag in video');
		$fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/Place" />');
		$fieldConfigArray[] = $fieldConfig;

	    $fieldConfig = new DistributionFieldConfig();
	    $fieldConfig->setFieldName(FacebookDistributionField::TAGS);
	    $fieldConfig->setUserFriendlyFieldName('IDs (semicolon separated) of persons to tag in video');
	    $fieldConfig->setEntryMrssXslt('<xsl:value-of select="customData/metadata/Tags" />');
	    $fieldConfigArray[$fieldConfig->getFieldName()] = $fieldConfig;

		return $fieldConfigArray;
	}

	public function getApiAuthorizeUrl()
	{
		if ($this->getPageId())
		{
			$permissions = $this->getFacebookPermissions();
			$url = kConf::get('apphome_url');
			$url .= "/index.php/extservices/facebookoauth2".
				"/".FacebookConstants::FACEBOOK_PARTNER_ID_REQUEST_PARAM."/".base64_encode($this->getPartnerId()).
				"/".FacebookConstants::FACEBOOK_PROVIDER_ID_REQUEST_PARAM."/".base64_encode($this->getId()).
				"/".FacebookConstants::FACEBOOK_PAGE_ID_REQUEST_PARAM."/".base64_encode($this->getPageId()).
				"/".FacebookConstants::FACEBOOK_PERMISSIONS_REQUEST_PARAM."/".base64_encode($permissions).
				"/".FacebookConstants::FACEBOOK_RE_REQUEST_PERMISSIONS_REQUEST_PARAM."/".base64_encode($this->getReRequestPermissions())
			;
			return $url;
		}
		return null;

	}

	private function validateVideo(flavorAsset $flavorAsset)
	{
		$syncKey = $flavorAsset->getSyncKey(flavorAsset::FILE_SYNC_FLAVOR_ASSET_SUB_TYPE_ASSET);
		if(kFileSyncUtils::fileSync_exists($syncKey))
		{
			$videoAssetFilePath = kFileSyncUtils::getLocalFilePathForKey($syncKey, false);
			$mediaInfo = mediaInfoPeer::retrieveByFlavorAssetId($flavorAsset->getId());
			if(!$mediaInfo)
				return false;
			try
			{
				FacebookGraphSdkUtils::validateVideoAttributes($videoAssetFilePath, $mediaInfo->getFileSize(), $mediaInfo->getVideoDuration());
				return true;
			}
			catch(Exception $e)
			{
				KalturaLog::debug('Asset ['.$flavorAsset->getId().'] not valid for distribution: '.$e->getMessage());
			}
		}
		return false;
	}

}