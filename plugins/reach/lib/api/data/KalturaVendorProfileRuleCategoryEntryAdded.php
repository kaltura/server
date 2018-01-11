<?php
/**
 * @package plugins.reach
 * @subpackage api.objects
 */
class KalturaVendorProfileRuleCategoryEntryAdded extends KalturaVendorProfileRuleOption
{
	/**
	 * @var string
	 */
	public $categoryId;

	private static $map_between_objects = array
	(
		"categoryId",
	);

	public function getMapBetweenObjects ( )
	{
		return array_merge ( parent::getMapBetweenObjects() , self::$map_between_objects );
	}

	public function getVendorProfileRule($params = null)
	{
//		$rule = new KalturaVendorProfileRule();
//		$rule->eventObjectType = KalturaVendorProfileEventObjectType::CATEGORYENTRY;
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