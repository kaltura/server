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
	 * @var string
	 * @readonly
	 * @filter order
	 */
	public $createdAt;
	
	/**
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
	
	
	private static $map_between_objects = array
	(
		"id" , "name", "website" => "url1" , "notificationUrl" => "url2" , "appearInSearch" , "createdAt" , "adminName" , "adminEmail" ,
		"description" , "commercialUse" , "landingPage" , "userLandingPage" , "contentCategories" , "type" , "phone" , "describeYourself" ,
		"adultContent" , "defConversionProfileType" , "notify" , "status" , "allowQuickEdit" , "mergeEntryLists" , "notificationsConfig" ,
		"maxUploadSize" , "partnerPackage" , "secret" , "adminSecret" , "allowMultiNotification", "adminLoginUsersQuota", "adminUserId",
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
	
	public function fromObject ( $source_object  )
	{
		return self::fromPartner($source_object);
	}
	
	public function toPartner()
	{
		$partner = new Partner();
		return parent::toObject( $partner );
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