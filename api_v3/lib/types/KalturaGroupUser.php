<?php
/**
 * @package api
 * @subpackage objects
 */
class KalturaGroupUser extends KalturaObject implements IFilterable
{

	/**
	 * @var string
	 * @insertonly
	 * @filter eq,in
	 */
	public $userId;
	
	/**
	 * @var string
	 * @insertonly
	 * @filter eq,in
	 */
	public $groupId;

	/**
	 * @var KalturaGroupUserStatus
	 * @readonly
	 * @filter eq,in
	 */
	public $status;

	/**
	 * @var int
	 * @readonly
	 * @filter eq
	 */
	public $partnerId;

	/**
	 * Creation date as Unix timestamp (In seconds)
	 *
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $createdAt;

	/**
	 * Last update date as Unix timestamp (In seconds)
	 *
	 * @var time
	 * @readonly
	 * @filter gte,lte,order
	 */
	public $updatedAt;
	
	private static $map_between_objects = array
	(
		"userId" => "puser_id",
		"groupId" => "pgroup_id",
		"partnerId",
		"status",
		"createdAt",
		"updatedAt",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function toObject($dbObject = null, $skip = array())
	{
		if (is_null($dbObject))
			$dbObject = new KuserKgroup();
			
		parent::toObject($dbObject, $skip);
		
		return $dbObject;
	}

	/* (non-PHPdoc)
	 * @see KalturaObject::validateForInsert()
	 */
	public function validateForInsert($propertiesToSkip = array())
	{

		//verify kuser exists
		$kuser = kuserPeer::getKuserByPartnerAndUid( $this->userId , $this->partnerId , false , KuserType::USER);
		if (! $kuser)
			throw new KalturaAPIException ( KalturaErrors::USER_NOT_FOUND, $this->userId );

		//verify group exists
		$kgroup = kuserPeer::getKuserByPartnerAndUid( $this->groupId , $this->partnerId , false , KuserType::GROUP);
		if (! $kgroup)
			throw new KalturaAPIException ( KalturaErrors::GROUP_NOT_FOUND, $this->userId );

		//verify kuser does not belongs to kgroup
		$kuserKgroup = KuserKgroupPeer::getByKuserIdAndKgroupId($kuser->getId(), $kgroup->getId());
		if($kuserKgroup)
			throw new KalturaAPIException (KalturaErrors::GROUP_USER_ALREADY_EXISTS);

		//verify user does not belongs to more than max allowed groups
		$kuserKgroups = KuserKgroupPeer::getKgroupsByKuserId($kuser->getId());
		if(!is_null($kuserKgroups) && is_array($kuserKgroups)){
			if ( count($kuserKgroup) > KuserKgroup::MAX_NUMBER_OF_GROUPS_PER_USER){
				throw new KalturaAPIException (KalturaErrors::USER_EXCEEDED_MAX_GROUPS);
			}
		}

		parent::validateForInsert ( $propertiesToSkip );
	}
	
	
	public function fromObject($sourceObject)
	{
		if(!$sourceObject)
			return;
			
		parent::fromObject($sourceObject);
	}
	
	public function getExtraFilters()
	{ 
		return array();		
	}
	
	public function getFilterDocs()
	{
		return array();	
	}
}
?>