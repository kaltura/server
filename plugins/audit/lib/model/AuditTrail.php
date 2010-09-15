<?php


/**
 * Skeleton subclass for representing a row from the 'audit_trail' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class AuditTrail extends BaseAuditTrail 
{	
	private static $allwodObjectTypes = array(
		KalturaAuditTrailObjectType::ACCESS_CONTROL,
		KalturaAuditTrailObjectType::ADMIN_KUSER,
		KalturaAuditTrailObjectType::BATCH_JOB,
		KalturaAuditTrailObjectType::CATEGORY,
		KalturaAuditTrailObjectType::CONVERSION_PROFILE_2,
		KalturaAuditTrailObjectType::EMAIL_INGESTION_PROFILE,
		KalturaAuditTrailObjectType::ENTRY,
		KalturaAuditTrailObjectType::FILE_SYNC,
		KalturaAuditTrailObjectType::FLAVOR_ASSET,
		KalturaAuditTrailObjectType::FLAVOR_PARAMS,
		KalturaAuditTrailObjectType::FLAVOR_PARAMS_CONVERSION_PROFILE,
		KalturaAuditTrailObjectType::FLAVOR_PARAMS_OUTPUT,
		KalturaAuditTrailObjectType::KSHOW,
		KalturaAuditTrailObjectType::KSHOW_KUSER,
		KalturaAuditTrailObjectType::KUSER,
		KalturaAuditTrailObjectType::MEDIA_INFO,
		KalturaAuditTrailObjectType::MODERATION,
		KalturaAuditTrailObjectType::PARTNER,
		KalturaAuditTrailObjectType::PUSER_KUSER,
		KalturaAuditTrailObjectType::ROUGHCUT,
		KalturaAuditTrailObjectType::SYNDICATION,
		KalturaAuditTrailObjectType::UI_CONF,
		KalturaAuditTrailObjectType::UPLOAD_TOKEN,
		KalturaAuditTrailObjectType::WIDGET,
		KalturaAuditTrailObjectType::METADATA,
		KalturaAuditTrailObjectType::METADATA_PROFILE,
	);
	
	private static $uniqueRequestId = null;
	
	public function __construct()
	{
		$this->setRequestId($this->getUniqueRequestId());
		$this->setContext($this->getContext());
		$this->setMasterPartnerId(kCurrentContext::$ks_partner_id);
		$this->setKuserId(kCurrentContext::$uid);
		$this->setKs(kCurrentContext::$ks);
		$this->setIpAddress(kCurrentContext::$user_ip);
		$this->setServerName(kCurrentContext::$host);
		$this->setEntryPoint(kCurrentContext::getEntryPoint());
		$this->setUserAgent(requestUtils::getRemoteUserAgent());
	}

	/**
	 * @return int unique id per request
	 */
	public function getUniqueRequestId() 
	{
		if(!is_null(self::$uniqueRequestId))
			return self::$uniqueRequestId;
			
		$dcId = kDataCenterMgr::getCurrentDcId();
		for($i = 0; $i < 10; ++$i)
		{
			$requestId = $dcId . '_' . kString::generateStringId();
			$exists = AuditTrailPeer::retrieveByRequestId( $requestId );
			
			if(!$exists)
			{
				self::$uniqueRequestId = $requestId;
				return self::$uniqueRequestId;
			}
		}
		
		throw new kAuditTrailException('Unable to generate unique id', kAuditTrailException::UNIQUE_ID_NOT_GENERATED);
	}

	/**
	 * @return int context
	 */
	public function getContext() 
	{
		switch (kCurrentContext::$ps_vesion) 
		{
			case 'ps2':
				return KalturaAuditTrailContext::PS2;
			
			case 'ps3':
				return KalturaAuditTrailContext::API_V3;
			
			default:
				return KalturaAuditTrailContext::SCRIPT;
		}
	}
	
	/* (non-PHPdoc)
	 * @see audit/lib/model/om/BaseAuditTrail#setObjectType()
	 */
	public function setObjectType($v)
	{
		if(!in_array($v, self::$allwodObjectTypes))
			throw new kAuditTrailException("Object type [$v] not allowed", kAuditTrailException::OBJECT_TYPE_NOT_ALLOWED);
		
		return parent::setObjectType($v);
	} // setObjectType()
	
	/* (non-PHPdoc)
	 * @see audit/lib/model/om/BaseAuditTrail#setRelatedObjectType()
	 */
	public function setRelatedObjectType($v)
	{
		if(!in_array($v, self::$allwodObjectTypes))
			throw new kAuditTrailException("Object type [$v] not allowed", kAuditTrailException::OBJECT_TYPE_NOT_ALLOWED);
		
		return parent::setRelatedObjectType($v);
	} // setRelatedObjectType()

	/**
	 * Serialize the object and set the value of [data] column.
	 * 
	 * @param      KalturaAuditTrailInfo $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setData($v)
	{
		$data = null;
		if($v instanceof KalturaAuditTrailInfo)
			$data = serialize($v);
		
		return parent::setData($data);
	} // setData()

	/**
	 * Get the [data] column value and unserialize to object.
	 * 
	 * @return     KalturaAuditTrailInfo
	 */
	public function getData()
	{
		$data = parent::getData();
		if(is_null($data))
			return null;
			
		try{
			return unserialize($data);
		}
		catch(Exception $e){
			return null;
		}
	} 
	
	/* (non-PHPdoc)
	 * @see audit/lib/model/om/BaseAuditTrail#save()
	 */
	public function save(PropelPDO $con = null)
	{
		if(kAuditTrailManager::traceEnabled($this->getPartnerId(), $this))
			return parent::save($con);
			
		KalturaLog::debug("No audit created object type [$this->object_type] action [$this->action]");
		return 0;
	} // save()
	
} // AuditTrail
