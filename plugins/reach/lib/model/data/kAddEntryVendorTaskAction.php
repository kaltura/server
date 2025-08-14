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

	/**
	 * @var int
	 */
	protected $entryObjectType;
	
	public function __construct() 
	{
		parent::__construct(ReachPlugin::getRuleActionTypeCoreValue(ReachRuleActionType::ADD_ENTRY_VENDOR_TASK));
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


	public function getEntryObjectType()
	{
		return $this->entryObjectType;
	}

	/**
	 * @param int $entryObjectType
	 */
	public function setEntryObjectType($entryObjectType)
	{
		$this->entryObjectType = $entryObjectType;
	}
}
