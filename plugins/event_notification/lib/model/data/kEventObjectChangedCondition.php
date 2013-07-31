<?php
/**
 * @package plugins.eventNotification
 * @subpackage model.data
 */
class kEventObjectChangedCondition extends kCondition
{
	/**
	 * Comma seperated column names to be tested
	 * @var string
	 */
	protected $modifiedColumns;
	
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(EventNotificationPlugin::getConditionTypeCoreValue(EventNotificationConditionType::EVENT_NOTIFICATION_OBJECT_CHANGED));
		parent::__construct($not);
	}
	
	/* (non-PHPdoc)
	 * @see kCondition::applyDynamicValues()
	 */
	protected function applyDynamicValues(kScope $scope)
	{
		parent::applyDynamicValues($scope);
		
		$dynamicValues = $scope->getDynamicValues('{', '}');
		
		if(is_array($dynamicValues) && count($dynamicValues))
		{
			$this->modifiedColumns = str_replace(array_keys($dynamicValues), $dynamicValues, $this->modifiedColumns);
		}
	}

	/* (non-PHPdoc)
	 * @see kCondition::internalFulfilled()
	 */
	protected function internalFulfilled(kScope $scope)
	{
		if(!($scope instanceof kEventScope))
			return false;
			
		$event = $scope->getEvent();
		if(!($event instanceof kObjectChangedEvent))
			return false;
			
		$trigerColumns = explode(',', $this->modifiedColumns);
		$modifiedColumns = array_intersect($event->getModifiedColumns(), $trigerColumns);
		
		KalturaLog::debug("Triger columns [" . print_r($trigerColumns, true) . "]");
		KalturaLog::debug("Found columns [" . print_r($modifiedColumns, true) . "]");
		
		return count($modifiedColumns) > 0;
	}
	
	/**
	 * @return string $modifiedColumns
	 */
	public function getModifiedColumns()
	{
		return $this->modifiedColumns;
	}

	/**
	 * @param string $modifiedColumns
	 */
	public function setModifiedColumns($modifiedColumns)
	{
		$this->modifiedColumns = $modifiedColumns;
	}
}
