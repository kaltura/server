<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaCategoryUserAdvancedFilter extends KalturaSearchItem
{
	/**
	 * @var string
	 */
	public $memberIdEq;
	
	/**
	 * @var string
	 */
	public $memberIdIn;
	
	/**
	 * @var string
	 */
	public $memberPermissionsMatchOr;
	
	/**
	 * @var string
	 */
	public $memberPermissionsMatchAnd;
	
	private static $map_between_objects = array
	(
		"memberPermissionsMatchOr",
		"memberPermissionsMatchAnd",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}
	
	public function toObject ( $obj = null , $props_to_skip = array() )
	{
		if(!$obj)
			$obj = new kCategoryKuserAdvancedFilter();
		
		if (!$this->memberIdEq && !$this->memberIdIn)
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, 'memberIdEq,memberIdIn');
		}
		
		if (!$this->memberPermissionsMatchOr && !$this->memberPermissionsMatchAnd)
		{
			throw new KalturaAPIException(KalturaErrors::PROPERTY_VALIDATION_CANNOT_BE_NULL, 'memberIdEq,memberIdIn');
		}
		
		if ($this->memberIdEq)
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid(kCurrentContext::getCurrentPartnerId(), $this->memberIdEq);
			if (!$kuser)
			{
				throw new KalturaAPIException (KalturaErrors::USER_NOT_FOUND);
			}
			$obj->setMemberIdIn(array($kuser->getId()));
		}
		
		if ($this->memberIdIn)
		{
			$kusers = kuserPeer::getKuserByPartnerAndUids(kCurrentContext::getCurrentPartnerId(), explode(',', $this->memberIdIn));
			$kuserIds = array();
			if (!$kusers || !count($kusers))
				throw new KalturaAPIException (KalturaErrors::USER_NOT_FOUND);
			
			foreach($kusers as $kuser)
			{
				$kuserIds[] = $kuser->getId();
			}
			
			$obj->setMemberIdIn($kuserIds);
		}
			
		return parent::toObject($obj, $props_to_skip);
	}
}