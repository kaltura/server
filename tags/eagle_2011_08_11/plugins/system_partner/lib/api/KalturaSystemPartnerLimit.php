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
			case KalturaSystemPartnerLimitType::ENTRIES:
				$limit->max = $partner->getEntriesQuota();
				$limit->overagePrice = $partner->getEntriesOveragePrice();
				break;
				
			case KalturaSystemPartnerLimitType::MONTHLY_STREAM_ENTRIES:
				$limit->max = $partner->getStreamEntriesQuota();
				$limit->overagePrice = $partner->getStreamEntriesOveragePrice();
				break;
				
			case KalturaSystemPartnerLimitType::MONTHLY_BANDWIDTH:
				$limit->max = $partner->getBandwidthQuota();
				$limit->overagePrice = $partner->getBandwidthOveragePrice();
				break;
				
			case KalturaSystemPartnerLimitType::PUBLISHERS:
				$limit->max = $partner->getPublishersQuota();
				$limit->overagePrice = $partner->getPublishersOveragePrice();
				break;
				
			case KalturaSystemPartnerLimitType::LOGIN_USERS:
				$limit->max = $partner->getLoginUsersQuota();
				$limit->overagePrice = $partner->getLoginUsersOveragePrice();
				break;
				
			case KalturaSystemPartnerLimitType::ADMIN_LOGIN_USERS:
				$limit->max = $partner->getAdminLoginUsersQuota();
				$limit->overagePrice = $partner->getAdminLoginUsersOveragePrice();
				break;
				
			case KalturaSystemPartnerLimitType::USER_LOGIN_ATTEMPTS:
				$limit->max = $partner->getMaxLoginAttempts();
				$limit->overagePrice = $partner->getMaxLoginAttemptsOveragePrice();
				break;
			
			case KalturaSystemPartnerLimitType::BULK_SIZE:
				$limit->max = $partner->getMaxBulkSize();
				$limit->overagePrice = $partner->getMaxBulkSizeOveragePrice();
				break;
				
			case KalturaSystemPartnerLimitType::MONTHLY_STORAGE:
				$limit->max = $partner->getMonthlyStorage();
				$limit->overagePrice = $partner->getMonthlyStorageOveragePrice();
				break;
				
			case KalturaSystemPartnerLimitType::MONTHLY_STORAGE_AND_BANDWIDTH:
				$limit->max = $partner->getMonthlyStorageAndBandwidth();
				$limit->overagePrice = $partner->getMonthlyStorageAndBandwidthOveragePrice();
				break;	

			case KalturaSystemPartnerLimitType::END_USERS:
				$limit->max = $partner->getEndUsers();
				$limit->overagePrice = $partner->getEndUsersOveragePrice();
				break;		
				
				
		}
		return $limit;
	} 

	public function validate()
	{
		switch($this->type)
		{
			case KalturaSystemPartnerLimitType::ENTRIES:
				$this->validatePropertyMinValue('max', 0, true);
				$this->validatePropertyMinValue('overagePrice', 0, true);
				break;
				
			case KalturaSystemPartnerLimitType::MONTHLY_STREAM_ENTRIES:
				$this->validatePropertyMinValue('max', 0, true);
				$this->validatePropertyMinValue('overagePrice', 0, true);
				break;
				
			case KalturaSystemPartnerLimitType::MONTHLY_BANDWIDTH:
				$this->validatePropertyMinValue('max', 0, true);
				$this->validatePropertyMinValue('overagePrice', 0, true);
				break;
				
			case KalturaSystemPartnerLimitType::PUBLISHERS:
				$this->validatePropertyMinValue('max', 0, true);
				$this->validatePropertyMinValue('overagePrice', 0, true);
				break;
				
			case KalturaSystemPartnerLimitType::LOGIN_USERS:
				$this->validatePropertyMinValue('max', 1, true);
				$this->validatePropertyMinValue('overagePrice', 0, true);
				break;
				
			case KalturaSystemPartnerLimitType::ADMIN_LOGIN_USERS:
				$this->validatePropertyMinValue('max', 0, true);
				$this->validatePropertyMinValue('overagePrice', 0, true);
				break;
				
			case KalturaSystemPartnerLimitType::USER_LOGIN_ATTEMPTS:
				$this->validatePropertyMinValue('max', 0, true);
				$this->validatePropertyMinValue('overagePrice', 0, true);
				break;
			
			case KalturaSystemPartnerLimitType::BULK_SIZE:
				$this->validatePropertyMinValue('max', 0, true);
				$this->validatePropertyMinValue('overagePrice', 0, true);
				break;

			case KalturaSystemPartnerLimitType::MONTHLY_STORAGE:
				$this->validatePropertyMinValue('max', 0, true);
				$this->validatePropertyMinValue('overagePrice', 0, true);
				break;

			case KalturaSystemPartnerLimitType::MONTHLY_STORAGE_AND_BANDWIDTH:
				$this->validatePropertyMinValue('max', 0, true);
				$this->validatePropertyMinValue('overagePrice', 0, true);
				break;
				
			case KalturaSystemPartnerLimitType::END_USERS:
				$this->validatePropertyMinValue('max', 0, true);
				$this->validatePropertyMinValue('overagePrice', 0, true);
				break;	
				
		}
	}
	
	/**
	 * @param Partner $partner
	 */
	public function apply(Partner $partner)
	{
		switch($this->type)
		{
			case KalturaSystemPartnerLimitType::ENTRIES:
				$partner->setEntriesQuota($this->max);
				$partner->setEntriesOveragePrice($this->overagePrice);
				break;
				
			case KalturaSystemPartnerLimitType::MONTHLY_STREAM_ENTRIES:
				$partner->setStreamEntriesQuota($this->max);
				$partner->setStreamEntriesOveragePrice($this->overagePrice);
				break;
				
			case KalturaSystemPartnerLimitType::MONTHLY_BANDWIDTH:
				$partner->setBandwidthQuota($this->max);
				$partner->setBandwidthOveragePrice($this->overagePrice);
				break;
				
			case KalturaSystemPartnerLimitType::PUBLISHERS:
				$partner->setPublishersQuota($this->max);
				$partner->setPublishersOveragePrice($this->overagePrice);
				break;
				
			case KalturaSystemPartnerLimitType::LOGIN_USERS:
				$partner->setLoginUsersQuota($this->max);
				$partner->setLoginUsersOveragePrice($this->overagePrice);
				break;
				
			case KalturaSystemPartnerLimitType::ADMIN_LOGIN_USERS:
				$partner->setAdminLoginUsersQuota($this->max);
				$partner->setAdminLoginUsersOveragePrice($this->overagePrice);
				break;
			
			case KalturaSystemPartnerLimitType::USER_LOGIN_ATTEMPTS:
				$partner->setMaxLoginAttempts($this->max);
				$partner->setMaxLoginAttemptsOveragePrice($this->overagePrice);
				break;
			
			case KalturaSystemPartnerLimitType::BULK_SIZE:
				$partner->setMaxBulkSize($this->max);
				$partner->setMaxBulkSizeOveragePrice($this->overagePrice);
				break;
				
			case KalturaSystemPartnerLimitType::MONTHLY_STORAGE:
				$partner->setMonthlyStorage($this->max);
				$partner->setMonthlyStorageOveragePrice($this->overagePrice);
				break;
				
			case KalturaSystemPartnerLimitType::MONTHLY_STORAGE_AND_BANDWIDTH:
				$partner->setMonthlyStorageAndBandwidth($this->max);
				$partner->setMonthlyStorageAndBandwidthOveragePrice($this->overagePrice);
				break;	

			case KalturaSystemPartnerLimitType::END_USERS:
				$partner->setEndUsers($this->max);
				$partner->setEndUsersOveragePrice($this->overagePrice);
				break;	
				
		}
	} 
}