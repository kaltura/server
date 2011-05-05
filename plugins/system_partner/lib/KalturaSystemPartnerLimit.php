<?php
/**
 * @package plugins.systemPartner
 * @subpackage api.objects
 */
class KalturaSystemPartnerLimit extends KalturaObject
{
	/**
	 * @var KalturaSystemPartnerLimitType
	 */
	public $type;
	
	/**
	 * @var int
	 */
	public $max;
	
	/**
	 * @var float
	 */
	public $overagePrice;
	
	/**
	 * @param KalturaSystemPartnerLimitType $type
	 * @param Partner $partner
	 * @return KalturaSystemPartnerLimit
	 */
	public static function fromPartner($type, Partner $partner)
	{
		$limit = new KalturaSystemPartnerLimit();
		$limit->type = $type;
		
		switch($type)
		{
			case ENTRIES:
				$limit->max = $partner->getEntriesQuota();
				$limit->overagePrice = $partner->getEntriesOveragePrice();
				break;
				
			case STREAM_ENTRIES:
				$limit->max = $partner->getStreamEntriesQuota();
				$limit->overagePrice = $partner->getStreamEntriesOveragePrice();
				break;
				
			case BANDWIDTH:
				$limit->max = $partner->getBandwidthQuota();
				$limit->overagePrice = $partner->getBandwidthOveragePrice();
				break;
				
			case PUBLISHERS:
				$limit->max = $partner->getPublishersQuota();
				$limit->overagePrice = $partner->getPublishersOveragePrice();
				break;
				
			case ADMIN_USERS:
				$limit->max = $partner->getLoginUsersQuota();
				$limit->overagePrice = $partner->getLoginUsersOveragePrice();
				break;
				
			case END_USERS:
				$limit->max = $partner->getAdminLoginUsersQuota();
				$limit->overagePrice = $partner->getAdminLoginUsersOveragePrice();
				break;
		}
		return $limit;
	} 
	
	/**
	 * @param Partner $partner
	 */
	public function apply(Partner $partner)
	{
		switch($limit->type)
		{
			case ENTRIES:
				$partner->setEntriesQuota($limit->max);
				$partner->setEntriesOveragePrice($limit->overagePrice);
				break;
				
			case STREAM_ENTRIES:
				$partner->setStreamEntriesQuota($limit->max);
				$partner->setStreamEntriesOveragePrice($limit->overagePrice);
				break;
				
			case BANDWIDTH:
				$partner->setBandwidthQuota($limit->max);
				$partner->setBandwidthOveragePrice($limit->overagePrice);
				break;
				
			case PUBLISHERS:
				$partner->setPublishersQuota($limit->max);
				$partner->setPublishersOveragePrice($limit->overagePrice);
				break;
				
			case ADMIN_USERS:
				$partner->setLoginUsersQuota($limit->max);
				$partner->setLoginUsersOveragePrice($limit->overagePrice);
				break;
				
			case END_USERS:
				$partner->setAdminLoginUsersQuota($limit->max);
				$partner->setAdminLoginUsersOveragePrice($limit->overagePrice);
				break;
		}
	} 
}