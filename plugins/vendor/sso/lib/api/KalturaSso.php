<?php
/**
 * @package api
 * @subpackage objects
 * @relatedService SsoService
 */
class KalturaSso extends KalturaObject implements IRelatedFilterable
{
	const APPLICATION_TYPE = 'ApplicationType';

	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;

	/**
	 * @var KalturaApplicationType
	 * @insertonly
	 */
	public $applicationId;

	/**
	 * @var int
	 * @readonly
	 * @filter eq
	 */
	public $partnerId;

	/**
	 * @var KalturaSsoStatus
	 * @filter eq,in
	 */
	public $status;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	/**
	 * Last update date as Unix timestamp (In seconds)
	 * @var time
	 * @readonly
	 */
	public $updatedAt;

	/**
	* Redirect URL for a specific application id and partner id
	* @var string
	*/
	public $redirectUrl;

	private static $map_between_objects = array
	(
		"id",
		"applicationId" => "accountId",
		"partnerId",
		"status",
		"createdAt",
		"updatedAt",
		"redirectUrl",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
		{
			$dbObject = new SsoVendorIntegration();
			$dbObject->setVendorType(VendorTypeEnum::SSO);
		}

		parent::toObject($dbObject, $skip);
		return $dbObject;
	}

	public function getExtraFilters()
	{
		return array();
	}

	public function getFilterDocs()
	{
		return array();
	}

	/* (non-PHPdoc)
 	 * @see KalturaObject::toInsertableObject()
 	 */
	public function toInsertableObject($object_to_fill = null, $props_to_skip = array())
	{
		if (is_null($object_to_fill))
		{
			$object_to_fill = new SsoVendorIntegration();
			$object_to_fill->setVendorType(VendorTypeEnum::SSO);
		}
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('redirectUrl');
		$this->validatePropertyNotNull('applicationId');
		$applicationId = SsoPlugin::getCoreValue(self::APPLICATION_TYPE, $this->applicationId);
		$existingSso = VendorIntegrationPeer::getVendorByPartnerAccountIdVendorType($applicationId, kCurrentContext::getCurrentPartnerId(), VendorTypeEnum::SSO);
		if ($existingSso)
		{
			throw new KalturaAPIException(KalturaSsoErrors::DUPLICATE_SSO, $existingSso->getId());
		}
		parent::validateForInsert($propertiesToSkip);
	}
}