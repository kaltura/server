<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaCategoryUserFilter extends KalturaCategoryUserBaseFilter
{
	static private $map_between_objects = array
	(
		"freeText" => "_mlikeor_screen_name-puser_id",
		"categoryDirectMembers" => "_category_direct_members",
	);

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new categoryKuserFilter();
	}
	
	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}
	
	
	/**
	 * Return the list of categoryUser that are not inherited from parent category - only the direct categoryUsers.
	 * @var bool
	 * @requiresPermission read
	 */
	public $categoryDirectMembers;
	
	/**
	 * Free text search on user id or screen name
	 * @var string
	 */
	public $freeText;

	/**
	 * Return a list of categoryUser that related to the userId in this field by groups
	 * @var string
	 */
	public $relatedGroupsByUserId;

	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaResponseProfileBase $responseProfile = null)
	{
		if($this->userIdIn)
		{
			$usersIds = explode(',', $this->userIdIn);
			$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;

			$c = new Criteria();
			$c->add(kuserPeer::PARTNER_ID, $partnerId, Criteria::EQUAL);
			$c->add(kuserPeer::PUSER_ID, $usersIds, Criteria::IN);
			$kusers = kuserPeer::doSelect($c);
			
			$usersIds = array();
			foreach($kusers as $kuser)
			{
				/* @var $kuser kuser */
				$usersIds[] = $kuser->getId();
			}
				
			$this->userIdIn = implode(',', $usersIds);
		}

		if ($this->relatedGroupsByUserId){
			$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
			$userIds = array();
			$c = new Criteria();
			$c->add(kuserPeer::PARTNER_ID, $partnerId);
			$c->add(kuserPeer::PUSER_ID, $this->relatedGroupsByUserId);
			$c->add(kuserPeer::TYPE, KuserType::USER);
			$kuser = kuserPeer::doSelectOne($c);
			if (!$kuser){
				$response = new KalturaCategoryUserListResponse();
				$response->objects = new KalturaCategoryUserArray();
				$response->totalCount = 0;
				return $response;
			}

			$kgroupIds = KuserKgroupPeer::retrieveKgroupIdsByKuserId($kuser->getId());
			if (!is_null($kgroupIds) && is_array($kgroupIds))
				$userIds = $kgroupIds;
			$userIds[] = $kuser->getId();

			// if userIdIn is also set in the filter need to intersect the two arrays.
			if(isset($this->userIdIn)){
				$curUserIds = explode(',',$this->userIdIn);
				$userIds = array_intersect($curUserIds, $userIds);
			}

			$this->userIdIn = implode(',', $userIds);
		}
		
		if($this->userIdEqual)
		{
			$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
			
			$c = new Criteria();
			$c->add(kuserPeer::PARTNER_ID, $partnerId);
			$c->add(kuserPeer::PUSER_ID, $this->userIdEqual);
			
			if (kCurrentContext::$ks_partner_id == Partner::BATCH_PARTNER_ID) //batch should be able to get categoryUser of deleted users.
				kuserPeer::setUseCriteriaFilter(false);

			// in case of more than one deleted kusers - get the last one
			$c->addDescendingOrderByColumn(kuserPeer::UPDATED_AT);

			$kuser = kuserPeer::doSelectOne($c);
			kuserPeer::setUseCriteriaFilter(true);
			
			if (!$kuser)
			{
				KalturaLog::debug('User not found');
				$response = new KalturaCategoryUserListResponse();
        		$response->objects = new KalturaCategoryUserArray();
        		$response->totalCount = 0;
        		
        		return $response;
			}
				
			$this->userIdEqual = $kuser->getId();
		}	

		$categories = array();
		if ($this->categoryIdEqual)
		{
			$categories[] = categoryPeer::retrieveByPK($this->categoryIdEqual);
		}
		elseif($this->categoryIdIn)
		{
			$categories = categoryPeer::retrieveByPKs(explode(',', $this->categoryIdIn));
		}
		
		$categoriesInheritanceRoot = array();
		foreach ($categories as $category)
		{
			/* @var $category category */
			if(is_null($category))
				continue;
				
			if($category->getInheritanceType() == InheritanceType::INHERIT)
			{
				if($this->categoryDirectMembers && kCurrentContext::$master_partner_id == Partner::BATCH_PARTNER_ID)
				{
					$categoriesInheritanceRoot[$category->getId()] = $category->getId();
				}
				else
				{
					//if category inheris members - change filter to -> inherited from parent id = category->getIheritedParent
					$categoriesInheritanceRoot[$category->getInheritedParentId()] = $category->getInheritedParentId();	
				}
			}
			else
			{
				$categoriesInheritanceRoot[$category->getId()] = $category->getId();
			}
		}
		$this->categoryDirectMembers = null;
		$this->categoryIdEqual = null;
		$this->categoryIdIn = implode(',', $categoriesInheritanceRoot);

		//if filter had categories that doesn't exists or not entitled - should return 0 objects. 
		if(count($categories) && !count($categoriesInheritanceRoot))
		{
			$response = new KalturaCategoryUserListResponse();
			$response->totalCount = 0;
			
			return $response;
		}
		
		$categoryKuserFilter = $this->toObject();
		
		$c = KalturaCriteria::create(categoryKuserPeer::OM_CLASS);
		$categoryKuserFilter->attachToCriteria($c);
		$pager->attachToCriteria($c);
		$c->applyFilters();
		
		$list = categoryKuserPeer::doSelect($c);
		$totalCount = $c->getRecordsCount();
		
		$newList = KalturaCategoryUserArray::fromDbArray($list, $responseProfile);
		
		$response = new KalturaCategoryUserListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		
		return $response;
	}
}
