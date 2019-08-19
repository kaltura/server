<?php
/**
 * @package api
 * @subpackage objects
 * @relatedService SsoService
 */
class KalturaSso extends KalturaObject implements IRelatedFilterable
{
	const APPLICATION_TYPE = 'ApplicationType';
	const PARTNER_ID = 'partner Id';
	const APPLICATION_ID = 'application Id';

	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;

	/**
	 * @var KalturaApplicationType
	 */
	public $applicationId;

	/**
	 * @var int
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
	 * @filter gte,lte,order
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

	public function validateParameters()
	{
		$redirectUrl = $this->redirectUrl;
		$applicationId = $this->applicationId;

		if (!$redirectUrl)
		{
			throw new KalturaAPIException(KalturaSsoErrors::MISSING_MANDATORY_PARAMETER, "Redirect url is missing", $redirectUrl);
		}
		if (!$applicationId)
		{
			throw new KalturaAPIException(KalturaSsoErrors::MISSING_MANDATORY_PARAMETER, "Application type is missing", $redirectUrl);
		}
		if (!$this->partnerId)
		{
			$this->partnerId = kCurrentContext::getCurrentPartnerId();
		}
	}

	public function validateDuplication()
	{
		$applicationId = SsoPlugin::getCoreValue(self::APPLICATION_TYPE, $this->applicationId);
		$existingSso = VendorIntegrationPeer::getVendorByPartnerAccountIdVendorType($applicationId, $this->partnerId, VendorTypeEnum::SSO);
		if ($existingSso)
		{
			throw new KalturaAPIException(KalturaSsoErrors::DUPLICATE_SSO, $existingSso->getId());
		}
	}

	public function validateForUpdate($sourceObject, $propertiesToSkip = array())
	{
		$this->validateUpdatableFields();
		parent::validateForUpdate($sourceObject, $propertiesToSkip);
	}

	protected function validateUpdatableFields()
	{
		if ($this->partnerId)
		{
			throw new KalturaAPIException(KalturaSsoErrors::CANNOT_UPDATE_PARAMETER, self::PARTNER_ID);
		}
		if ($this->applicationId)
		{
			throw new KalturaAPIException(KalturaSsoErrors::CANNOT_UPDATE_PARAMETER, self::APPLICATION_ID);
		}
	}
}