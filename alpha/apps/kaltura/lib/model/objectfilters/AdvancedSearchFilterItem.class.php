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
	
	public $filterLimit;
	
	public $overrideFilterLimit;

	final public function apply(baseObjectFilter $filter, IKalturaDbQuery $query)
	{
		if($this->overrideFilterLimit)
		{
			$filter->setLimit($this->overrideFilterLimit);
		}
		$this->filterLimit = $filter->getLimit();
		$this->applyCondition($query);
	}
	
	public function getFreeTextConditions($partnerScope, $freeTexts)
	{
		return array();	
	}
	
	/**
	 * Adds conditions, matches and where clauses to the query
	 * @param IKalturaIndexQuery $query
	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
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
