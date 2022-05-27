<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaAppToken extends KalturaObject implements IFilterable 
{
	/**
	 * The id of the application token
	 * 
	 * @var string
	 * @readonly
	 * @filter eq,in
	 */
	public $id;
	
	/**
	 * The application token
	 * 
	 * @var string
	 * @readonly
	 */
	public $token;
	
	/**
	 * @var int
	 * @readonly
	 */
	public $partnerId;
	
	/**
	 * Creation time as Unix timestamp (In seconds) 
	 * 
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;
	
	/**
	 * Update time as Unix timestamp (In seconds) 
	 * 
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	/**
	 * Application token status 
	 * 
	 * @var KalturaAppTokenStatus
	 * @readonly
	 * @filter eq,in
	 */
	public $status;

	/**
	 * Expiry time of current token (unix timestamp in seconds)
	 * 
	 * @var time
	 */
	public $expiry;

	/**
	 * Type of KS (Kaltura Session) that created using the current token
	 * 
	 * @var KalturaSessionType
	 */
	public $sessionType;

	/**
	 * User id of KS (Kaltura Session) that created using the current token
	 * 
	 * @var string
	 * @filter eq
	 */
	public $sessionUserId;

	/**
	 * Expiry duration of KS (Kaltura Session) that created using the current token (in seconds)
	 * 
	 * @var int
	 */
	public $sessionDuration;

	/**
	 * Comma separated privileges to be applied on KS (Kaltura Session) that created using the current token
	 * @var string
	 */
	public $sessionPrivileges;

	/**
	 * @var KalturaAppTokenHashType
	 */
	public $hashType;

	/**
	 *
	 * @var string
	 */
	public $description;

	private static $mapBetweenObjects = array
	(
		"id",
		"partnerId",
		"createdAt",
		"updatedAt",
		"status",
		"token",
		"expiry",
		"sessionUserId",
		"sessionType",
		"sessionDuration",
		"sessionPrivileges",
		'hashType',
		'description'
	);
	
	/* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$mapBetweenObjects);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toObject()
	 */
	public function toObject($dbAppToken = null, $skip = array())
	{
		if(!$dbAppToken)
			$dbAppToken = new AppToken();
			
		return parent::toObject($dbAppToken, $skip);
	}
	
	/* (non-PHPdoc)
	 * @see KalturaObject::toInsertableObject()
	 */
	public function toInsertableObject($dbAppToken = null, $skip = array())
	{
		$partnerId = kCurrentContext::getCurrentPartnerId();
		$partner = PartnerPeer::retrieveByPK($partnerId);
		if($this->isNull('sessionDuration'))
		{
			$this->sessionDuration = $partner->getKsMaxExpiryInSeconds();
		}
		$dbAppToken = parent::toInsertableObject($dbAppToken, $skip);
		
		/* @var $dbAppToken AppToken */
		$dbAppToken->setPartnerId($partnerId);
		$dbAppToken->setToken(bin2hex(openssl_random_pseudo_bytes(16)));
		$dbAppToken->setStatus(AppTokenStatus::ACTIVE);
		$dbAppToken->setKuserIdBySessionUserId();

		return $dbAppToken;
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::toUpdatableObject()
	 */
	public function toUpdatableObject($dbAppToken, $skip = array())
	{
		$dbAppToken = parent::toUpdatableObject($dbAppToken, $skip);

		/* @var $dbAppToken AppToken */
		$dbAppToken->setKuserIdBySessionUserId();
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getExtraFilters()
	 */
	public function getExtraFilters()
	{
		return array();
	}
	
	/* (non-PHPdoc)
	 * @see IFilterable::getFilterDocs()
	 */
	public function getFilterDocs()
	{
		return array();
	}
	
}
