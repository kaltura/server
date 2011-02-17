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
 * @package plugins.audit
 * @subpackage model
 */
class AuditTrail extends BaseAuditTrail 
{	
	protected $puserId = null;
	
	const AUDIT_TRAIL_CONTEXT_CLIENT = -1;
	const AUDIT_TRAIL_CONTEXT_SCRIPT = 0;
	const AUDIT_TRAIL_CONTEXT_PS2 = 1;
	const AUDIT_TRAIL_CONTEXT_API_V3 = 2;
	
	const AUDIT_TRAIL_STATUS_PENDING = 1;
	const AUDIT_TRAIL_STATUS_READY = 2;
	const AUDIT_TRAIL_STATUS_FAILED = 3;
	
	const AUDIT_TRAIL_ACTION_CREATED = 'CREATED';
	const AUDIT_TRAIL_ACTION_COPIED = 'COPIED';
	const AUDIT_TRAIL_ACTION_CHANGED = 'CHANGED';
	const AUDIT_TRAIL_ACTION_DELETED = 'DELETED';
	const AUDIT_TRAIL_ACTION_VIEWED = 'VIEWED';
	const AUDIT_TRAIL_ACTION_CONTENT_VIEWED = 'CONTENT_VIEWED';
	const AUDIT_TRAIL_ACTION_FILE_SYNC_CREATED = 'FILE_SYNC_CREATED';
	const AUDIT_TRAIL_ACTION_RELATION_ADDED = 'RELATION_ADDED';
	const AUDIT_TRAIL_ACTION_RELATION_REMOVED = 'RELATION_REMOVED';
	
	private static $allwodObjectTypes;
	
	public static function getAllwodObjectTypes()
	{
		if(!self::$allwodObjectTypes)
		{
			$reflect = new ReflectionClass('AuditTrailObjectType');
			self::$allwodObjectTypes = $reflect->getConstants();
		}
		
		return self::$allwodObjectTypes;
	}
	
	private static $uniqueRequestId = null;
	
	public function __construct()
	{	
		parent::__construct();
		
		$this->setContext($this->getDefaultContext());
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
	public function getDefaultContext() 
	{
		switch (kCurrentContext::$ps_vesion) 
		{
			case 'ps2':
				return self::AUDIT_TRAIL_CONTEXT_PS2;
			
			case 'ps3':
				return self::AUDIT_TRAIL_CONTEXT_API_V3;
			
			default:
				return self::AUDIT_TRAIL_CONTEXT_SCRIPT;
		}
	}
	
	/* (non-PHPdoc)
	 * @see audit/lib/model/om/BaseAuditTrail#setObjectType()
	 */
	public function setObjectType($v)
	{
		if(!in_array($v, self::getAllwodObjectTypes()))
			throw new kAuditTrailException("Object type [$v] not allowed", kAuditTrailException::OBJECT_TYPE_NOT_ALLOWED);
		
		return parent::setObjectType($v);
	} // setObjectType()
	
	/* (non-PHPdoc)
	 * @see audit/lib/model/om/BaseAuditTrail#setRelatedObjectType()
	 */
	public function setRelatedObjectType($v)
	{
		if(!in_array($v, self::getAllwodObjectTypes()))
			throw new kAuditTrailException("Object type [$v] not allowed", kAuditTrailException::OBJECT_TYPE_NOT_ALLOWED);
		
		return parent::setRelatedObjectType($v);
	} // setRelatedObjectType()

	/**
	 * Serialize the object and set the value of [data] column.
	 * 
	 * @param      kAuditTrailInfo $v new value
	 * @return     AuditTrail The current object (for fluent API support)
	 */
	public function setData($v)
	{
		$data = null;
		if($v instanceof kAuditTrailInfo)
			$data = serialize($v);
		
		return parent::setData($data);
	} // setData()

	/**
	 * Get the [data] column value and unserialize to object.
	 * 
	 * @return     kAuditTrailInfo
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
	
	public function getPuserId()
	{
		if(!$this->puserId)
		{
			$kuser = KuserPeer::retrieveByPK($this->getKuserId());
			if($kuser)
				$this->puserId = $kuser->getPuserId(); 
		}
			
		return $this->puserId;
	}
	
	public function setPuserId($v)
	{
		$this->puserId = $v;
		$kuser = KuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $this->puserId, true);
		if($kuser)
			return $this->setKuserId($kuser->getId());
	}
	
	/* (non-PHPdoc)
	 * @see audit/lib/model/om/BaseAuditTrail#save()
	 */
	public function save(PropelPDO $con = null)
	{
		if(!kAuditTrailManager::traceEnabled($this->getPartnerId(), $this))
		{
			KalturaLog::debug("No audit created object type [$this->object_type] action [$this->action]");
			return 0;
		}

		if(is_null($this->getKuserId()))
		{
			$this->setPuserId(kCurrentContext::$uid);
		}
	
		if(is_null($this->getClientTag()))
			$this->setClientTag(kCurrentContext::$client_lang);
		
		$this->setRequestId($this->getUniqueRequestId());
		$this->setMasterPartnerId(kCurrentContext::$master_partner_id);
		$this->setKs(kCurrentContext::$ks);
		$this->setIpAddress(kCurrentContext::$user_ip);
		$this->setServerName(kCurrentContext::$host);
		$this->setEntryPoint(kCurrentContext::getEntryPoint());
		$this->setUserAgent(requestUtils::getRemoteUserAgent());
		
		return parent::save($con);
	} // save()
	
} // AuditTrail
