<?php
/**
 * @package Scheduler
 * @subpackage Client
 */
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAuditTrailAction
{
	const CREATED = "CREATED";
	const COPIED = "COPIED";
	const CHANGED = "CHANGED";
	const DELETED = "DELETED";
	const VIEWED = "VIEWED";
	const CONTENT_VIEWED = "CONTENT_VIEWED";
	const FILE_SYNC_CREATED = "FILE_SYNC_CREATED";
	const RELATION_ADDED = "RELATION_ADDED";
	const RELATION_REMOVED = "RELATION_REMOVED";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAuditTrailContext
{
	const CLIENT = -1;
	const SCRIPT = 0;
	const PS2 = 1;
	const API_V3 = 2;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAuditTrailObjectType
{
	const ACCESS_CONTROL = "accessControl";
	const ADMIN_KUSER = "adminKuser";
	const BATCH_JOB = "BatchJob";
	const CATEGORY = "category";
	const CONVERSION_PROFILE_2 = "conversionProfile2";
	const EMAIL_INGESTION_PROFILE = "EmailIngestionProfile";
	const ENTRY = "entry";
	const FILE_SYNC = "FileSync";
	const FLAVOR_ASSET = "flavorAsset";
	const FLAVOR_PARAMS = "flavorParams";
	const FLAVOR_PARAMS_CONVERSION_PROFILE = "flavorParamsConversionProfile";
	const FLAVOR_PARAMS_OUTPUT = "flavorParamsOutput";
	const KSHOW = "kshow";
	const KSHOW_KUSER = "KshowKuser";
	const KUSER = "kuser";
	const MEDIA_INFO = "mediaInfo";
	const MODERATION = "moderation";
	const PARTNER = "Partner";
	const ROUGHCUT = "roughcutEntry";
	const SYNDICATION = "syndicationFeed";
	const UI_CONF = "uiConf";
	const UPLOAD_TOKEN = "UploadToken";
	const WIDGET = "widget";
	const METADATA = "Metadata";
	const METADATA_PROFILE = "MetadataProfile";
	const USER_LOGIN_DATA = "UserLoginData";
	const USER_ROLE = "UserRole";
	const PERMISSION = "Permission";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAuditTrailOrderBy
{
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const PARSED_AT_ASC = "+parsedAt";
	const PARSED_AT_DESC = "-parsedAt";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAuditTrailStatus
{
	const PENDING = 1;
	const READY = 2;
	const FAILED = 3;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaAuditTrailBaseFilter extends KalturaFilter
{
	/**
	 * 
	 *
	 * @var int
	 */
	public $idEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $createdAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $parsedAtGreaterThanOrEqual = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $parsedAtLessThanOrEqual = null;

	/**
	 * 
	 *
	 * @var KalturaAuditTrailStatus
	 */
	public $statusEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $statusIn = null;

	/**
	 * 
	 *
	 * @var KalturaAuditTrailObjectType
	 */
	public $auditObjectTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $auditObjectTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objectIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $objectIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $relatedObjectIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $relatedObjectIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaAuditTrailObjectType
	 */
	public $relatedObjectTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $relatedObjectTypeIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $masterPartnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $masterPartnerIdIn = null;

	/**
	 * 
	 *
	 * @var int
	 */
	public $partnerIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $partnerIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $requestIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $requestIdIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIdEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $userIdIn = null;

	/**
	 * 
	 *
	 * @var KalturaAuditTrailAction
	 */
	public $actionEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $actionIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ksEqual = null;

	/**
	 * 
	 *
	 * @var KalturaAuditTrailContext
	 */
	public $contextEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $contextIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryPointEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $entryPointIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serverNameEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $serverNameIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ipAddressEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $ipAddressIn = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $clientTagEqual = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAuditTrailFilter extends KalturaAuditTrailBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaAuditClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaAuditClientPlugin
	 */
	protected static $instance;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaAuditClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaAuditClientPlugin($client);
		return self::$instance;
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'audit';
	}
}

