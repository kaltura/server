
<?php

/**
 * Vendor Profile Rules
 *
 * @package plugins.reach
 * @subpackage model
 *
 */
class kVendorProfileRule
{
	/**
	 * @var bool
	 */	
	protected $manualDispatchEnabled;
	
	/**
	 * @var bool
	 */
	protected $automaticDispatchEnabled;
	
	/**
	 * @var string
	 */
	protected $eventType;
	
	/**
	 * @var string
	 */
	protected $eventObjectType;
	
	/**
	 * @var array<kCondition> $eventConditions
	 */
	protected $eventConditions;
	
	/**
	 * @var string
	 */
	protected $catalogItemIds;
	
	/**
	 * @return the $manualDispatchEnabled
	 */
	public function getManualDispatchEnabled() { return $this->manualDispatchEnabled; }
	
	/**
	 * @param bool $manualDispatchEnabled
	 */
	public function setManualDispatchEnabled($manualDispatchEnabled) { $this->manualDispatchEnabled = $manualDispatchEnabled; }
	
	/**
	 * @return the $automaticDispatchEnabled
	 */
	public function getAutomaticDispatchEnabled() { return $this->automaticDispatchEnabled; }
	
	/**
	 * @param bool $automaticDispatchEnabled
	 */
	public function setAutomaticDispatchEnabled($automaticDispatchEnabled) { $this->automaticDispatchEnabled = $automaticDispatchEnabled; }
	
	/**
	 * @return the $eventType
	 */
	public function getEventType() { return $this->eventType; }
	
	/**
	 * @param bool $eventType
	 */
	public function setEventType($eventType) { $this->eventType = $eventType; }
	
	/**
	 * @return the $eventObjectType
	 */
	public function getEventObjectType() { return $this->eventObjectType; }
	
	/**
	 * @param string $eventObjectType
	 */
	public function setEventObjectType($eventObjectType) { $this->eventObjectType = $eventObjectType; }
	
	/**
	 * @return the $eventConditions
	 */
	public function getEventConditions() { return $this->eventConditions; }
	
	/**
	 * @param array $eventConditions
	 */
	public function setEventConditions($eventConditions) { $this->eventConditions = $eventConditions; }
	
	/**
	 * @return the $catalogItemIds
	 */
	public function getCatalogItemIds() { return $this->catalogItemIds; }
	
	/**
	 * @param string $catalogItemIds
	 */
	public function setCatalogItemIds($catalogItemIds) { $this->catalogItemIds = $catalogItemIds; }
}