<?php
/**
 * @package plugins.schedule
 * @subpackage api.objects
 * @relatedService ResourceUserService
 */
class KalturaResourceUser extends KalturaObject implements IRelatedFilterable
{
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;

	/**
	 * @var string
	 * @filter eq,in
	 * @insertOnly
	 */
	public $resourceTag;

	/**
	 * @var string
	 * @filter eq, in
	 * @insertOnly
	 * @requiresPermissions all
	 */
	public $userId;

	/**
	 * Status
	 *
	 * @var KalturaResourceUserStatus
	 * @readonly
	 * @filter eq,in
	 */
	public $status;

	/**
	 * ResourceUser creation date as Unix timestamp (In seconds)
	 *
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	/**
	 * ResourceUser update date as Unix timestamp (In seconds)
	 *
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;


	/*
	 * Mapping between the field on this object (on the left) and the setter/getter on the entry object (on the right)
	 */
	private static $map_between_objects = array
	(
		'partnerId',
		'userId' => 'kuserId',
		'status',
		'resourceTag',
		'createdAt',
		'updatedAt',
	);

	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert($propertiesToSkip)
	 */
	public function validateForInsert($propertiesToSkip = array())
	{
		$this->validatePropertyNotNull('resourceTag');
		$this->validatePropertyNotNull('userId');

		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		$kuser = kuserPeer::getKuserByPartnerAndUid($partnerId, $this->userId );

		if(!$kuser)
		{
			throw new KalturaAPIException(KalturaErrors::USER_ID_NOT_FOUND, $this->userId);
		}

		$c = new Criteria();
		$c->add(ResourceUserPeer::RESOURCE_TAG, $this->resourceTag);
		$c->add(ResourceUserPeer::KUSER_ID, $kuser->getId());
		if(ResourceUserPeer::doCount($c))
		{
			throw new KalturaAPIException(KalturaScheduleErrors::RESOURCE_USER_ALREADY_EXISTS);
		}

		return parent::validateForInsert($propertiesToSkip);
	}

	protected function doFromObject($srcObj, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$kuser = kuserPeer::retrieveByPK($srcObj->getKuserId());
		if ($kuser)
		{
			$this->userId = $kuser->getPuserId();
		}
		parent::doFromObject($srcObj, $responseProfile);
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject ( $object_to_fill = null , $props_to_skip = array() )
	{
		$object_to_fill = parent::toInsertableObject($object_to_fill, $props_to_skip);

		$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::$ks_partner_id, $this->userId);
		if (!$kuser)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_USER_ID, $this->userId);
		}
		$object_to_fill->setKuserId($kuser->getKuserId());
		$object_to_fill->setStatus(ResourceUserStatus::ACTIVE);
		$object_to_fill->setPartnerId(kCurrentContext::getCurrentPartnerId());
		return $object_to_fill;
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toObject($object_to_fill, $props_to_skip)
	 */
	public function toObject($sourceObject = null, $propertiesToSkip = array())
	{
		if(!$sourceObject)
		{
			$sourceObject = new ResourceUser();
		}

		return parent::toObject($sourceObject, $propertiesToSkip);
	}

	/**
	 * @inheritDoc
	 */
	function getExtraFilters()
	{
	    // TODO: Implement getExtraFilters() method.
	}

	/**https://github.com/kaltura/server/pull/11826#discussion_r1003551861
	 * @inheritDoc
	 */
	function getFilterDocs()
	{
	    // TODO: Implement getFilterDocs() method.
	}
}