<?php
/**
 * @package plugins.eventNotification
 * @subpackage model.data
 */
class kEventFieldCondition extends kCondition
{
	/* (non-PHPdoc)
	 * @see kCondition::__construct()
	 */
	public function __construct($not = false)
	{
		$this->setType(EventNotificationPlugin::getConditionTypeCoreValue(EventNotificationConditionType::EVENT_NOTIFICATION_FIELD));
		parent::__construct($not);
	}

	/**
	 * Needed in order to migrate old kEventFieldCondition that serialized before kCondition defined as parent class
	 */
	public function __wakeup()
	{
		$this->setType(EventNotificationPlugin::getConditionTypeCoreValue(EventNotificationConditionType::EVENT_NOTIFICATION_FIELD));
	}
	
	/**
	 * The field to evaluate against the values
	 * @var kBooleanField
	 */
	private $field;

	/* (non-PHPdoc)
	 * @see kCondition::internalFulfilled()
	 */
	protected function internalFulfilled(kScope $scope)
	{
		$this->field->setScope($scope);
		return $this->field->getValue();
	}
	
	/**
	 * @return kBooleanField
	 */
	public function getField() 
	{
		return $this->field;
	}

	/**
	 * @param kBooleanField $field
	 */
	public function setField(kBooleanField $field) 
	{
		$this->field = $field;
	}
}
