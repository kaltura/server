<?php
/**
 * @package Core
 * @subpackage model.filters.advanced
 */ 
class AdvancedSearchFilterComparableCondition extends AdvancedSearchFilterCondition
{
	/**
	 * @var KalturaSearchConditionComparison
	 */
	public $comparison;

	/**
	 * Adds conditions, matches and where clauses to the query
	 * @param IKalturaIndexQuery $query
	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
		$newCondition = $this->getCondition();
		if ($newCondition)
		{
			$query->addCondition($newCondition);
		}
	}

	public function getCondition()
	{
		switch ($this->getComparison())
		{
			case KalturaSearchConditionComparison::EQUAL:
				$comparison = ' = ';
				break;
			case KalturaSearchConditionComparison::GREATER_THAN:
				$comparison = ' > ';
				break;
			case KalturaSearchConditionComparison::GREATER_THAN_OR_EQUAL:
				$comparison = ' >= ';
				break;
			case KalturaSearchConditionComparison::LESS_THAN:
				$comparison = " < ";
				break;
			case KalturaSearchConditionComparison::LESS_THAN_OR_EQUAL:
				$comparison = " <= ";
				break;
			case KalturaSearchConditionComparison::NOT_EQUAL:
				$comparison = " <> ";
				break;
			default:
				KalturaLog::err("Missing comparison type");
				return;
		}

		$field = $this->getField();
		$value = $this->getValue();
		$fieldValue = $this->getFieldValue($field);
		if (is_null($fieldValue))
		{
			KalturaLog::err('Unknown field [' . $field . ']');
			return;
		}

		$newCondition = $fieldValue . $comparison . KalturaCriteria::escapeString($value);

		return $newCondition;
	}


	protected function getFieldValue($field)
	{
		$fieldValue = null;
		switch($field)
		{
			case Criteria::CURRENT_DATE:
				$d = getdate();
				$fieldValue = mktime(0, 0, 0, $d['mon'], $d['mday'], $d['year']);
				break;

			case Criteria::CURRENT_TIME:
			case Criteria::CURRENT_TIMESTAMP:
				$fieldValue = time();
				break;
		}
		return $fieldValue ;
	}

	public function addToXml(SimpleXMLElement &$xmlElement)
	{
		parent::addToXml($xmlElement);
		
		$xmlElement->addAttribute('comparison', htmlspecialchars($this->comparison));
	}
	
	public function fillObjectFromXml(SimpleXMLElement $xmlElement)
	{
		parent::fillObjectFromXml($xmlElement);
		
		$attr = $xmlElement->attributes();
		if(isset($attr['comparison']))
			$this->comparison = (string) html_entity_decode($attr['comparison']);
	}
	
	/**
	 * @return KalturaSearchConditionComparison $comparison
	 */
	public function getComparison() {
		return $this->comparison;
	}

	/**
	 * @param KalturaSearchConditionComparison $comparison the $comparison to set
	 */
	public function setComparison($comparison) {
		$this->comparison = $comparison;
	}
}
