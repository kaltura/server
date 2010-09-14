<?php
class KalturaAuditTrail extends KalturaObject implements IFilterable 
{
	/**
	 * @var int
	 * @filter eq
	 * @readonly
	 */
	public $id;
	
	/**
	 * @var int
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $createdAt;

	/**
	 * Indicates when the data was parsed
	 * @var int
	 * @filter gte,lte,order
	 * @readonly
	 */
	public $parsedAt;

	/**
	 * @var KalturaAuditTrailStatus
	 * @filter eq,in
	 * @readonly
	 */
	public $status;

	/**
	 * @var KalturaAuditTrailObjectType
	 * @filter eq,in
	 */
	public $objectType;

	/**
	 * @var string
	 * @filter eq,in
	 */
	public $objectId;

	/**
	 * @var string
	 * @filter eq,in
	 */
	public $relatedObjectId;

	/**
	 * @var KalturaAuditTrailObjectType
	 * @filter eq,in
	 */
	public $relatedObjectType;

	/**
	 * @var string
	 * @filter eq,in
	 */
	public $entryId;

	/**
	 * @var int
	 * @filter eq,in
	 * @readonly
	 */
	public $masterPartnerId;

	/**
	 * @var int
	 * @filter eq,in
	 * @readonly
	 */
	public $partnerId;

	/**
	 * @var string
	 * @filter eq,in
	 * @readonly
	 */
	public $requestId;

	/**
	 * @var string
	 * @filter eq,in
	 */
	public $puserId;

	/**
	 * @var KalturaAuditTrailAction
	 * @filter eq,in
	 */
	public $action;

	/**
	 * @var KalturaAuditTrailInfo
	 */
	public $data;

	/**
	 * @var string
	 * @readonly
	 */
	public $ks;

	/**
	 * @var KalturaAuditTrailContext
	 * @filter eq,in
	 * @readonly
	 */
	public $context;

	/**
	 * @var string
	 * @filter eq,in
	 * @readonly
	 */
	public $entryPoint;

	/**
	 * @var string
	 * @filter eq,in
	 * @readonly
	 */
	public $serverName;

	/**
	 * @var string
	 * @filter eq,in
	 * @readonly
	 */
	public $ipAddress;

	/**
	 * @var string
	 * @readonly
	 */
	public $userAgent;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var string
	 * @readonly
	 */
	public $errorDescription;
	
	private static $map_between_objects = array
	(
		"id",
		"createdAt",
		"parsedAt",
		"status",
		"objectType",
		"objectId",
		"relatedObjectId",
		"relatedObjectType",
		"entryId",
		"masterPartnerId",
		"partnerId",
		"requestId",
		"puserId",
		"action",
		"data",
		"ks",
		"context",
		"entryPoint",
		"serverName",
		"ipAddress",
		"userAgent",
		"description",
		"errorDescription",
	);
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	public function getExtraFilters()
	{
		return array();
	}
	
	public function getFilterDocs()
	{
		return array();
	}
	
	/**
	 * @param AuditTrail $dbAuditTrail
	 * @param array $propsToSkip
	 * @return AuditTrail
	 */
	public function toObject($dbAuditTrail = null, $propsToSkip = array())
	{
		if(is_null($dbAuditTrail))
			$dbAuditTrail = new AuditTrail();
			
		return parent::toObject($dbAuditTrail, $propsToSkip);
	}
	
	/**
	 * @param AuditTrail $dbAuditTrail
	 * @param array $propsToSkip
	 * @return AuditTrail
	 */
	public function toInsertableObject($dbAuditTrail = null, $propsToSkip = array())
	{
		if(is_null($dbAuditTrail))
			$dbAuditTrail = new AuditTrail();
			
		return $this->toInsertableObject($dbAuditTrail, $propsToSkip);
	}
}
