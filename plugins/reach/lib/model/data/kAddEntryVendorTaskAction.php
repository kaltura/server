<?php
/**
 * @package plugins.reach
 * @subpackage model.data
 */
class kAddEntryVendorTaskAction extends kRuleAction
{
	/**
	 * @var int
	 */
	protected $catalogItemId;
	
	public function __construct() 
	{
		parent::__construct(ReachRuleActionType::ADD_ENTRY_VENDOR_TASK);
	}
	
	/**
	 * @return the $catalogItemId
	 */
	public function getCatalogItemId() 
	{
		return $this->catalogItemId;
	}

	/**
	 * @param int $catalogItemId
	 */
	public function setCatalogItemId($catalogItemId) 
	{
		$this->catalogItemId = $catalogItemId;
	}
}
