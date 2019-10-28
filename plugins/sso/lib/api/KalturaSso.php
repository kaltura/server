<?php

/**
 * @package plugins.sso
 * @subpackage api.objects
 * @relatedService SsoService
 */
class KalturaSso extends KalturaObject implements IRelatedFilterable
{

	/**
	 * @var int
	 * @readonly
	 * @filter eq,in
	 */
	public $id;

	/**
	 * @var string
	 * @filter eq
	 */
	public $applicationType;

	/**
	 * @var int
	 * @insertonly
	 * @filter eq
	 */
	public $partnerId;

	/**
	 * @var string
	 * @filter eq
	 */
	public $domain;

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
	 * Redirect URL for a specific application type and (partner id or domain)
	 * @var string
	 * @filter eq
	 */
	public $redirectUrl;

	private static $map_between_objects = array
	(
		"id",
		"applicationType",
		"partnerId",
		"domain",
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
			$dbObject = new Sso();
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
			$object_to_fill = new Sso();
		}
		return parent::toInsertableObject($object_to_fill, $props_to_skip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('redirectUrl');
		$this->validatePropertyNotNull('applicationType');
		$this->validatePropertyNotNull('domain');
		if($this->partnerId === 0)
		{
			throw new KalturaAPIException(KalturaSsoErrors::PROPERTY_PARTNER_CANNOT_BE_0);
		}
		$existingSso = SsoPeer::getSso($this->applicationType, $this->partnerId, $this->domain);
		if ($existingSso)
		{
			throw new KalturaAPIException(KalturaSsoErrors::DUPLICATE_SSO, $existingSso->getId());
		}
		parent::validateForInsert($propertiesToSkip);
	}

	public static function getDomainFromUser($userId)
	{
		$domainPos = strpos($userId, '@');
		if ($domainPos === false || $domainPos + 1 === strlen($userId))	//domain not found
		{
			throw new KalturaAPIException(KalturaSsoErrors::SSO_NOT_FOUND);
		}
		return substr($userId,$domainPos + 1);
	}

	public static function getSso($partnerId, $applicationType, $domain)
	{
		$dbSso = SsoPeer::getSso($applicationType, $partnerId, $domain, SsoStatus::ACTIVE);
		if(!$dbSso)
		{
			throw new KalturaAPIException(KalturaSsoErrors::SSO_NOT_FOUND);
		}
		return $dbSso;
	}
}