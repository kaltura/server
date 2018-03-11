<?php
/**
 * @package plugins.reach
 * @subpackage model.data
 */
class kAddEntryVendorTaskAction extends kRuleAction
{
	/**
	 * @var string
	 */
	protected $catalogItemIds;
	
	public function __construct() 
	{
		parent::__construct(ReachRuleActionType::ADD_ENTRY_VENDOR_TASK);
	}
	
	/**
	 * @return the $catalogItemIds
	 */
	public function getCatalogItemIds()
	{
		return $this->catalogItemIds;
	}

	/**
	 * @param string $catalogItemIds
	 */
	public function setCatalogItemIds($catalogItemIds)
	{
		$this->catalogItemIds = $catalogItemIds;
	}
}
