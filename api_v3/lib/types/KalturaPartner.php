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
	 * @filter eq,in,order
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
	 * @var int
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
	 * @var int
	 * @readonly
	 * @filter eq,in,order
	 */
	public $status;
	
	/**
	 * @var int
	 */
	private $shouldForceUniqueKshow;
	
	/**
	 * @var int
	 */
	private $returnDuplicateKshow;
	
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
	 * @var int
	 */
	public $maxUploadSize;
	
	/**
	 * @var int
	 * @filter eq,gte,lte
	 * @readonly
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
	 * @insertonly
	 */
	public $additionalParams;
	
	private static $map_between_objects = array
	(
		"id" , "name", "website" => "url1" , "notificationUrl" => "url2" , "appearInSearch" , "createdAt" , "adminName" , "adminEmail" ,
		"description" , "commercialUse" , "landingPage" , "userLandingPage" , "contentCategories" , "type" , "phone" , "describeYourself" ,
		"adultContent" , "defConversionProfileType" , "notify" , "status" , "allowQuickEdit" , "mergeEntryLists" , "notificationsConfig" ,
		"maxUploadSize" , "partnerPackage" , "secret" , "adminSecret" , "allowMultiNotification", "adminLoginUsersQuota", "adminUserId",
		"firstName" , "lastName" , "country" , "state" , "additionalParams" ,
	);
	
	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}	
	
	public function fromPartner(Partner $partner)
	{
		parent::fromObject($partner);
		$this->additionalParams = KalturaKeyValueArray::fromKeyValueArray($this->additionalParams);
		return $this;
	}
	
	public function fromObject ( $source_object  )
	{
		return self::fromPartner($source_object);
	}
	
	public function toPartner()
	{
		if($this->adminName && $this->firstName === null && $this->lastName === null)
		{
			$this->firstName = $this->adminEmail;
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

		$partner = new Partner();
		$partner = parent::toObject( $partner );
		$additionalParamsArray = array();
		foreach($this->additionalParams as $pairObject)
		{
			$additionalParamsArray[$pairObject->key] = $pairObject->value;
			$partner->setAdditionalParams($additionalParamsArray);
		}
		return $partner;
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
	
}