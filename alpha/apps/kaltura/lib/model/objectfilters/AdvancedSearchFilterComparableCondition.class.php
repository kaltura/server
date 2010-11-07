<?php

class AdvancedSearchFilterComparableCondition extends AdvancedSearchFilterCondition
{
	/**
	 * @var KalturaSearchConditionComparison
	 */
	public $comparison;

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
