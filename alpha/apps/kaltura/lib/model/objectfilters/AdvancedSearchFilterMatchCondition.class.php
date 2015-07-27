<?php
/**
 * @package Core
 * @subpackage model.filters.advanced
 */ 
class AdvancedSearchFilterMatchCondition extends AdvancedSearchFilterCondition
{
	/**
	 * @var bool
	 */
	public $not;

	public function addToXml(SimpleXMLElement &$xmlElement)
	{
		parent::addToXml($xmlElement);
		
		$xmlElement->addAttribute('not', htmlspecialchars($this->not));
	}
	
	public function fillObjectFromXml(SimpleXMLElement $xmlElement)
	{
		parent::fillObjectFromXml($xmlElement);
		
		$attr = $xmlElement->attributes();
		if(isset($attr['not']))
			$this->not = (string) html_entity_decode($attr['not']);
	}
	
	/**
	 * @return bool $comparison
	 */
	public function getNot() {
		return $this->not;
	}

	/**
	 * @param bool $comparison
	 */
	public function setNot($not) {
		$this->not = $not;
	}
}
