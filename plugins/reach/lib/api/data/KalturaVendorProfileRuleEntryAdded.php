<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorProfileRuleEntryAdded extends KalturaVendorProfileRuleOption
{
	public function getVendorProfileRule($params = null)
	{
//		$rule = new KalturaVendorProfileRule();
//		$rule->eventObjectType = KalturaVendorProfileEventObjectType::ENTRY;
//		$rule->eventType = KalturaVendorProfileEventType::OBJECT_UPDATED;
//		$conditions = array();
//		$condition = new KalturaEventFieldCondition();
//		$field = new KalturaEvalBooleanField();
//		$field->code = 'in_array(entryPeer::STATUS, $scope->getEvent()->getModifiedColumns()) && $scope->getObject()->getStatus() == entryStatus::READY';
//		$condition->field = $field;
//		$conditions[] = $condition;
//		$rule->eventConditions = $conditions;
//		return $rule;
		return array();
	}
}