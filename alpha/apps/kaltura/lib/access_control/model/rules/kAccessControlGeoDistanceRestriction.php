<?php
/**
 * @package Core
 * @subpackage model.data
 * 
 * Old country restriction for backward compatibility
 */
class kAccessControlGeoDistanceRestriction extends kAccessControlRestriction
{
	/**
	 * @var kCountryCondition
	 */
	private $condition;
	
	/**
	 * @param accessControl $accessControl
	 */
	public function __construct(accessControl $accessControl = null)
	{
		parent::__construct($accessControl);
		$this->setActions(array(new kAccessControlAction(RuleActionType::BLOCK)));
		
		$this->condition = new kGeoDistanceCondition(true);
		$this->setConditions(array($this->getCondition()));
	}

	/**
	 * @return kCountryCondition
	 */
	protected function getCondition()
	{
		$conditions = $this->getConditions();
		if(!$this->condition && count($conditions))
			$this->condition = reset($conditions);
			
		return $this->condition;
	}

	/**
	 * @param string $getDistanceList
	 */
	function setGeoDistanceList($values)
	{
		$values = explode(',', $values);
		$stringValues = array();
		foreach($values as $value)
			$stringValues[] = new kStringValue($value);
			
		$this->getCondition()->setValues($stringValues);
	}
	
	/**
	 * @return string
	 */
	function getGeoDistanceList()
	{
		return implode(',', $this->getCondition()->getStringValues());
	}
}
