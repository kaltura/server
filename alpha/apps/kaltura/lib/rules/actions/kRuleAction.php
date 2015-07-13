<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kRuleAction 
{
	/**
	 * @var int RuleActionType
	 */
	protected $type;
	
	/**
	 * @param int $type RuleActionType
	 */
	public function __construct($type) 
	{
		$this->setType($type);
	}
	
	/**
	 * @return int RuleActionType
	 */
	public function getType() 
	{
		return $this->type;
	}

	/**
	 * @param int $type RuleActionType
	 */
	protected function setType($type) 
	{
		$this->type = $type;
	}
	
	public function applyDeliveryProfileDynamicAttributes(DeliveryProfileDynamicAttributes $deliveryAttributes)
	{
		return true;
	}
}
