<?php
/**
 * @package Core
 * @subpackage model.filters.advanced
 */ 
class AdvancedSearchFilterCondition extends AdvancedSearchFilterItem
{
	/**
	 * @var string
	 */
	protected $field;
	
	/**
	 * @var string
	 */
	protected $value;

	public function addToXml(SimpleXMLElement &$xmlElement)
	{
		parent::addToXml($xmlElement);
		
		$xmlElement->addAttribute('field', htmlspecialchars($this->field));
		$xmlElement->addAttribute('value', htmlspecialchars($this->value));
	}
	
	public function fillObjectFromXml(SimpleXMLElement $xmlElement)
	{
		parent::fillObjectFromXml($xmlElement);
		
		$attr = $xmlElement->attributes();
		if(isset($attr['field']))
			$this->field = (string) html_entity_decode($attr['field']);
			
		if(isset($attr['value']))
			$this->value = (string) html_entity_decode($attr['value']);
	}
	
	/**
	 * @return string $field
	 */
	public function getField() {
		return $this->field;
	}

	/**
	 * @return string $value
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * @return boolean $value
	 */
	public function getNot() {
		return $this->not;
	}

	/**
	 * @param boolean $value
	 */
	public function setNot($not)
	{
		$this->not = $not;
	}

	/**
	 * @param string $field the $field to set
	 */
	public function setField($field) {
		$this->field = $field;
	}

	/**
	 * @param string $value the $value to set
	 */
	public function setValue($value) {
		$this->value = $value;
	}
}
