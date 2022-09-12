<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaPartner extends KalturaObject implements IFilterable
{
	/**
	 * @var int
	 * @readonly
	 * @filter eq,in,order,notin
	 */
	public $id;
	
	/**
	 * @var string
	 * @filter like,mlikeor,mlikeand,eq,order
	 */
	public $name;
	
	/**
	 * @var string
	 * @filter order
	 */
	public $website;
	
	/**
	 * @var string
	 */
	public $notificationUrl;
	
	/**
	 * @var int
	 */
	public $appearInSearch;
	
	/**
	 * @var time
	 * @readonly
	 * @filter order
	 */
	public $createdAt;
	
	/**
	 * deprecated - lastName and firstName replaces this field
	 * @var string
	 * @filter order
	 */
	public $adminName;
	
	/**
	 * @var string
	 * @filter order
	 */
	public $adminEmail;
	
	/**
	 * @var string
	 */
	public $description;
	
	/**
	 * @var KalturaCommercialUseType
	 */
	public $commercialUse;
	
	/**
	 * @var string
	 */
	public $landingPage;
	
	/**
	 * @var string
	 */
	public $userLandingPage;
	
	/**
	 * @var string
	 */
	public $contentCategories;
	
	/**
	 * @var KalturaPartnerType
	 */
	public $type;
	
	/**
	 * @var string
	 */
	public $phone;
	
	/**
	 * @var string
	 */
	public $describeYourself;
	
	/**
	 * @var bool
	 */
	public $adultContent;
	
	/**
	 * @var string
	 */
	public $defConversionProfileType;
	
	/**
	 * @var int
	 */
	public $notify;
	
	/**
	 * @var KalturaPartnerStatus
	 * @readonly
	 * @filter eq,in,order
	 */
	public $status;
	
	/**
	 * @var int
	 */
	public $allowQuickEdit;
	
	/**
	 * @var int
	 */
	public $mergeEntryLists;
	
	/**
	 * @var string
	 */
	public $notificationsConfig;

	/**
	 * @var string
	 */
	public $allowedFromEmailWhiteList;
	
	/**
	 * @var int
	 */
	public $maxUploadSize;
	
	/**
	 * @var int
	 * @filter eq,gte,lte,in
	 * @requiresPermission update
	 */
	public $partnerPackage;
	
	/**
	 * @var string
	 * @readonly
	 * @requiresPermission read
	 */
	public $secret;
	
	/**
	 * @var string
	 * @readonly
	 * @requiresPermission read
	 */
	public $adminSecret;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $cmsPassword;

	/**
	 * @var int
	 */
	public $allowMultiNotification;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $adminLoginUsersQuota;
	
	/**
	 * @var string
	 */
	public $adminUserId;
	
	/**
	 * firstName and lastName replace the old (deprecated) adminName
	 * @var string
	 */
	public $firstName;

	/**
	 * lastName and firstName replace the old (deprecated) adminName
	 * @var string
	 */
	public $lastName;

	/**
	 * country code (2char) - this field is optional
	 * 
	 * @var string
	 */
	public $country;

	/**
	 * state code (2char) - this field is optional
	 * @var string
	 */
	public $state;
	
	/**
	 * @var KalturaKeyValueArray
	 */
	public $additionalParams;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $publishersQuota;
	
	/**
	 * @var KalturaPartnerGroupType
	 * @requiresPermission read
	 * @filter eq
	 * @readonly
	 */
	public $partnerGroupType;
	
	/**
	 * 
	 * @var bool
	 * @readonly
	 */
	public $defaultEntitlementEnforcement;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $defaultDeliveryType;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $defaultEmbedCodeType;
	
	/**
	 * @var KalturaPlayerDeliveryTypesArray
	 * @readonly
	 */
	public $deliveryTypes;
	
	/**
	 * @var KalturaPlayerEmbedCodeTypesArray
	 * @readonly
	 */
	public $embedCodeTypes;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $templatePartnerId;
	
	/**
	 * @var bool
	 * @readonly
	 */
	public $ignoreSeoLinks;

	/**
	 * @var bool
	 * @readonly
	 */
	public $blockDirectLogin;

	/**
	 * @var string
	 * @readonly
	 */
	public $host;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $cdnHost;
	
	/**
	 * @var bool
	 * @readonly
	 */
	public $isFirstLogin;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $logoutUrl;
	
	/**
	 * @var int
	 * @requiresPermission insert,update
	 */
	public $partnerParentId;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $crmId;

	/**
	 * @var string
	 */
	public $referenceId;
	
	/**
	 * @var bool
	 * @readonly
	 */
	public $timeAlignedRenditions;
	 
	/**
	* @var int
	* @readonly
	*/
	public $publisherEnvironmentType;

	/**
	 * @var string
	 * @readonly
	 */
	public $ovpEnvironmentUrl;

	/**
	 * @var string
	 * @readonly
	 */
	public $ottEnvironmentUrl;

	/**
	 * @var KalturaESearchLanguageArray
	 */
	public $eSearchLanguages;

	/**
	 * @var KalturaPartnerAuthenticationType
	 * @readonly
	 */
	public $authenticationType;

	/**
	 * @var string
	 * @readonly
	 */
	public $extendedFreeTrailExpiryReason;

	/**
	 *  Unix timestamp (In seconds)
	 * @var time
	 * @readonly
	 */
	public $extendedFreeTrailExpiryDate;

	/**
	 * @var int
	 * @readonly
	 */
	public $extendedFreeTrail;

	/**
	 * @var bool
	 * @readonly
	 */
	public $extendedFreeTrailEndsWarning;

	/**
	 * @var int
	 * @readonly
	 */
	public $eightyPercentWarning;

	/**
	 * @var int
	 * @readonly
	 */
	public $usageLimitWarning;

	/**
	 * @var int
	 * @readonly
	 */
	public $lastFreeTrialNotificationDay;

	/**
	 * @var int
	 * @filter eq
	 * @readonly
	 */
	public $monitorUsage;
	
	/**
	 * @var KalturaRegexArray
	 * @requiresPermission insert,update
	 */
	public $passwordStructureValidations;
	
	/**
	 * @var string
	 * @requiresPermission insert,update
	 */
	public $passwordStructureValidationsDescription;
	
	/**
	 * @var int
	 * @requiresPermission insert,update
	 */
	public $passReplaceFreq;
	
	/**
	 * @var int
	 * @requiresPermission insert,update
	 */
	public $maxLoginAttempts;
	
	/**
	 * @var int
	 * @requiresPermission insert,update
	 */
	public $loginBlockPeriod;
	
	/**
	 * @var int
	 * @requiresPermission insert,update
	 */
	public $numPrevPassToKeep;
	
	/**
	 * @var KalturaTwoFactorAuthenticationMode
	 * @readonly
	 */
	public $twoFactorAuthenticationMode;

	/**
	 * @var bool
	 * @requiresPermission insert,update
	 */
	public $isSelfServe;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $allowedDomains;
	
	/**
	 * @var string
	 * @readonly
	 */
	public $excludedAdminRoleName;
	
	private static $map_between_objects = array
	(
		'id' , 'name', 'website' => 'url1' , 'notificationUrl' => 'url2' , 'appearInSearch' , 'createdAt' , 'adminName' , 'adminEmail' , 'blockDirectLogin',
		'description' , 'commercialUse' , 'landingPage' , 'userLandingPage' , 'contentCategories' , 'type' , 'phone' , 'describeYourself' ,
		'adultContent' , 'defConversionProfileType' , 'notify' , 'status' , 'allowQuickEdit' , 'mergeEntryLists' , 'notificationsConfig' , 'allowedFromEmailWhiteList',
		'maxUploadSize' , 'partnerPackage' , 'secret' , 'adminSecret' , 'allowMultiNotification', 'adminLoginUsersQuota', 'adminUserId',
		'firstName' , 'lastName' , 'country' , 'state' , 'publishersQuota', 'partnerGroupType', 'defaultEntitlementEnforcement', 
		'defaultDeliveryType', 'defaultEmbedCodeType', 'deliveryTypes', 'embedCodeTypes',  'templatePartnerId', 'ignoreSeoLinks',
		'host', 'cdnHost', 'isFirstLogin', 'logoutUrl', 'partnerParentId','crmId', 'referenceId', 'timeAlignedRenditions','eSearchLanguages',
		'publisherEnvironmentType', 'ovpEnvironmentUrl', 'ottEnvironmentUrl', 'authenticationType', 'extendedFreeTrailExpiryReason', 'extendedFreeTrailExpiryDate',
		'extendedFreeTrail', 'extendedFreeTrailEndsWarning', 'eightyPercentWarning', 'usageLimitWarning', 'lastFreeTrialNotificationDay','monitorUsage', 'additionalParams',
		'passwordStructureValidations', 'passReplaceFreq', 'maxLoginAttempts', 'loginBlockPeriod', 'numPrevPassToKeep', 'twoFactorAuthenticationMode', 'isSelfServe','allowedDomains',
		'excludedAdminRoleName'
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}	
	
	public function fromPartner(Partner $partner)
	{
		parent::fromObject($partner);
		return $this;
	}
	
	public function doFromObject($partner, KalturaDetachedResponseProfile $responseProfile = null)
	{
		parent::doFromObject($partner);
		
		$this->updatePasswordStructureFromPartner($partner);
		
		$this->name = kString::stripUtf8InvalidChars($this->name);
		$this->description = kString::stripUtf8InvalidChars($this->description);
		$this->adminName = kString::stripUtf8InvalidChars($this->adminName);
		$this->describeYourself = kString::stripUtf8InvalidChars($this->describeYourself);
		$this->additionalParams = KalturaKeyValueArray::fromKeyValueArray($partner->getAdditionalParams());
		if (!$this->host){
			$this->host = null;
		}
		if (!$this->cdnHost){
			$this->cdnHost = null;
		}
		if (kCurrentContext::getCurrentPartnerId() > 0)
		{
			$this->extendedFreeTrailExpiryReason = null;
			$this->extendedFreeTrailExpiryDate = null;
			$this->extendedFreeTrail = null;
			$this->extendedFreeTrailEndsWarning = null;
			$this->eightyPercentWarning = null;
			$this->usageLimitWarning = null;
			$this->lastFreeTrialNotificationDay = null;
			$this->monitorUsage = null;
			if($partner->getHideSecrets() && kCurrentContext::$ks_partner_id > 0)
			{
				$this->adminSecret = null;
				$this->secret = null;
			}
		}
		
	}

	/**
	 * Function runs required validations on the current KalturaPartner object and 
	 * if all validations are successful, creates a new DB object for it and returns it.
	 * @throws KalturaAPIException
	 * @return Partner
	 */
	public function toPartner()
	{
		$vars_arr=get_object_vars($this);
		foreach ($vars_arr as $key => $val){
		    if (is_string($this->$key)){
                        $this->$key=strip_tags($this->$key);
                    }    
                }   
		
		if($this->adminName && $this->firstName === null && $this->lastName === null)
		{
			$this->firstName = $this->adminName;
		}
		elseif(
			($this->firstName || $this->lastName) &&
			($this->adminName === null || $this->adminName == "")
		)
		{
			$this->adminName = $this->firstName . " " . $this->lastName;
		}
		elseif(($this->firstName || $this->lastName) && $this->adminName)
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_DEPRECATED, "adminName");
		}

		$this->validatePropertyNotNull("name");
		$this->validatePropertyNotNull("adminName");
		$this->validatePropertyNotNull("adminEmail");
		$this->validatePropertyNotNull("description");
		$this->validatePropertyMaxLength("country", 2, true);
		$this->validatePropertyMaxLength("state", 2, true);
		$this->validatePartnerPackageForInsert();
		$this->validateForInsert();

		$partner = new Partner();
		return $this->toObject($partner);
	}
	
	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new Partner();
		}
		$dbObject = parent::toObject($dbObject);
		
		if(!is_null($this->passwordStructureValidations))
		{
			$dbObject->setPasswordStructureValidations(
				array(array(trim($this->passwordStructureValidations),
					trim($this->passwordStructureValidationsDescription))));
		}
		
		return $dbObject;
	}
	

	public function getExtraFilters()
	{
		return array(
			array("filter" => "like", "fields" => array("partnerName", "description", "website", "adminName", "adminEmail")),
		);
	}
	
	public function getFilterDocs()
	{
		return array();
	}

	public function validatePartnerPackageForInsert()
	{
		if (!$this->partnerPackage)
			return true;
		if (kCurrentContext::$ks_partner_id == Partner::ADMIN_CONSOLE_PARTNER_ID)
			return true;
		if (in_array($this->partnerPackage,kConf::get('allowed_partner_packages_for_all','local', array())))
			return true;
		throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_NO_INSERT_PERMISSION, 'partnerPackage');
	}
	
	protected function updatePasswordStructureFromPartner(Partner $partner)
	{
		$regexArr = $partner->getPasswordStructureRegex();
		if (!$regexArr)
		{
			$regexArr = kConf::get('user_login_password_structure');
		}
		$this->passwordStructureValidations = KalturaRegexArray::fromDbArray($regexArr);
		
		$this->passwordStructureValidationsDescription = $partner->getInvalidPasswordStructureMessage();
	}
}
