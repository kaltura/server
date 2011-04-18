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
class KalturaDocumentEntryOrderBy
{
	const NAME_ASC = "+name";
	const NAME_DESC = "-name";
	const MODERATION_COUNT_ASC = "+moderationCount";
	const MODERATION_COUNT_DESC = "-moderationCount";
	const CREATED_AT_ASC = "+createdAt";
	const CREATED_AT_DESC = "-createdAt";
	const UPDATED_AT_ASC = "+updatedAt";
	const UPDATED_AT_DESC = "-updatedAt";
	const RANK_ASC = "+rank";
	const RANK_DESC = "-rank";
	const PARTNER_SORT_VALUE_ASC = "+partnerSortValue";
	const PARTNER_SORT_VALUE_DESC = "-partnerSortValue";
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDocumentType
{
	const DOCUMENT = 11;
	const SWF = 12;
	const PDF = 13;
}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDocumentEntry extends KalturaBaseEntry
{
	/**
	 * The type of the document
	 *
	 * @var KalturaDocumentType
	 * @insertonly
	 */
	public $documentType = null;

	/**
	 * Conversion profile ID to override the default conversion profile
	 * 
	 *
	 * @var string
	 * @insertonly
	 */
	public $conversionProfileId = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
abstract class KalturaDocumentEntryBaseFilter extends KalturaBaseEntryFilter
{
	/**
	 * 
	 *
	 * @var KalturaDocumentType
	 */
	public $documentTypeEqual = null;

	/**
	 * 
	 *
	 * @var string
	 */
	public $documentTypeIn = null;


}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDocumentEntryFilter extends KalturaDocumentEntryBaseFilter
{

}

/**
 * @package Scheduler
 * @subpackage Client
 */
class KalturaDocumentClientPlugin extends KalturaClientPlugin
{
	/**
	 * @var KalturaDocumentClientPlugin
	 */
	protected static $instance;

	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaDocumentClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		if(!self::$instance)
			self::$instance = new KalturaDocumentClientPlugin($client);
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
		return 'document';
	}
}

