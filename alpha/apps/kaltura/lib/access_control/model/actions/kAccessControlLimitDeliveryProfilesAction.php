<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kAccessControlLimitDeliveryProfilesAction extends kRuleAction 
{
	/**
	 * @var array
	 */
	protected $deliveryProfileIds = array();
	
	/**
	 * @var bool
	 */
	protected $isBlockedList;
	
	
	public function __construct() 
	{
		parent::__construct(RuleActionType::LIMIT_DELIVERY_PROFILES);
	}
	
	/**
	 * @return array
	 */
	public function getDeliveryProfileIds() 
	{
		return $this->deliveryProfileIds;
	}

	/**
	 * @param array $deliveryProfileIds
	 */
	public function setDeliveryProfileIds($deliveryProfileIds) 
	{
		$this->deliveryProfileIds = $deliveryProfileIds;
	}
	
	/**
	 * @return the $isBlockedList
	 */
	public function getIsBlockedList() 
	{
		return $this->isBlockedList;
	}

	/**
	 * @param bool $isBlockedList
	 */
	public function setIsBlockedList($isBlockedList) 
	{
		$this->isBlockedList = $isBlockedList;
	}

	public function applyDeliveryProfileDynamicAttributes(DeliveryProfileDynamicAttributes $deliveryAttributes)
	{
		$deliveryAttributes->setDeliveryProfileIds($this->getDeliveryProfileIds(), $this->getIsBlockedList());	
	}
	
}
