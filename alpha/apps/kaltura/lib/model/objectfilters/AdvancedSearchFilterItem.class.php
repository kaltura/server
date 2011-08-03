<?php
/**
 * @package Core
 * @subpackage model.filters.advanced
 */ 
class AdvancedSearchFilterItem
{
	/**
	 * @var string
	 */
	protected $kalturaClass;
	
	public function apply(baseObjectFilter $filter, Criteria &$criteria, array &$matchClause, array &$whereClause, array &$conditionClause, array &$orderByClause)
	{
		$matchClause[] = $this->applyCondition($whereClause, $conditionClause);
	}
	
	public function getFreeTextConditions($freeTexts)
	{
		return array();	
	}
	
	/**
	 * 
	 * apply condition on whereClause and return array of strings for match clause
	 * @param Criteria $criteria
	 * @param array $whereClause
	 * return array
	 */
	public function applyCondition(array &$whereClause, array &$conditionClause)
	{
		return null;
	}
	
	public function addToXml(SimpleXMLElement &$xmlElement)
	{
		$xmlElement->addAttribute('kalturaClass', $this->kalturaClass);
	}
	
	public function fillObjectFromXml(SimpleXMLElement $xmlElement)
	{
		$attr = $xmlElement->attributes();
		if(isset($attr['kalturaClass']))
			$this->kalturaClass = (string) $attr['kalturaClass'];
	}
	
	/**
	 * @return the $kalturaClass
	 */
	public function getKalturaClass() {
		return $this->kalturaClass;
	}

	/**
	 * @param $kalturaClass the $kalturaClass to set
	 */
	public function setKalturaClass($kalturaClass) {
		$this->kalturaClass = $kalturaClass;
	}
}
