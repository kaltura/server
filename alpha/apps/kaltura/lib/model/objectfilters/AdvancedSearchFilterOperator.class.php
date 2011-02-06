<?php

class AdvancedSearchFilterOperator extends AdvancedSearchFilterItem
{
	const SEARCH_AND = 1;
	const SEARCH_OR = 2;
	
	/**
	 * @var int AND or OR
	 */
	protected $type;
	
	/**
	 * @var array
	 */
	protected $items;
	
	/**
	 * @return int $type
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return array $items
	 */
	public function getItems() {
		return $this->items;
	}

	/**
	 * @param int $type the $type to set
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @param array $items the $items to set
	 */
	public function setItems(array $items) {
		$this->items = $items;
	}

	/**
	 * @var string
	 */
	protected $condition = null;
	
	public function getCondition()
	{
		if($this->condition)
			return $this->condition;
			
		$conditions = array();
		
		if(count($this->items))
		{
			foreach($this->items as $item)
			{
				if($item instanceof AdvancedSearchFilterItem)
				{
					$condition = $item->getCondition();
					KalturaLog::debug("Append item [" . get_class($item) . "] condition [$condition]");
					if($condition)
						$conditions[] = "($condition)";
				}
			}
		}

		if(!count($conditions))
			return null;
			
		$glue = ($this->type == MetadataSearchFilter::SEARCH_AND ? ' & ' : ' | ');
		$this->condition = implode($glue, $conditions);
		
		return $this->condition;
	}
	
	public function getFreeTextConditions($freeTexts)
	{
		$additionalConditions = array();
		if(count($this->items))
		{
			foreach($this->items as $item)
			{
				if($item instanceof AdvancedSearchFilterItem)
				{
					$itemAdditionalConditions = $item->getFreeTextConditions($freeTexts);
					foreach($itemAdditionalConditions as $itemAdditionalCondition)
					{
						KalturaLog::debug("Append free text item [" . get_class($item) . "] condition [$itemAdditionalCondition]");
						$additionalConditions[] = "$itemAdditionalCondition";
					}
				}
			}
		}
		return $additionalConditions;
	}
	
	public function apply(baseObjectFilter $filter, Criteria &$criteria, array &$matchClause, array &$whereClause)
	{
		KalturaLog::debug("apply from [" . get_class($filter) . "]");
		
		$condition = $this->getCondition();
		if($condition && strlen($condition))
			$matchClause[] = "@plugins_data $condition";
	}
	
	public function addToXml(SimpleXMLElement &$xmlElement)
	{
		parent::addToXml($xmlElement);
		
		$xmlElement->addAttribute('operatorType', $this->type);
		
		if($this->items && is_array($this->items))
		{
			foreach($this->items as $item)
			{
				$itemXmlElement = $xmlElement->addChild('item');
				$itemXmlElement->addAttribute('type', get_class($item));
				
				$item->addToXml($itemXmlElement);
			}
		}
	}
	
	public function fillObjectFromXml(SimpleXMLElement $xmlElement)
	{
		parent::fillObjectFromXml($xmlElement);
		
		$attr = $xmlElement->attributes();
			
		if(isset($attr['operatorType']))
			$this->type = (int) $attr['operatorType'];
			
		foreach($xmlElement->item as $child)
		{
			$attr = $child->attributes();
			if(!isset($attr['type']))
				continue;
				
			$type = (string) $attr['type'];
			$item = new $type();
			$item->fillObjectFromXml($child);
			$this->items[] = $item;
		}
	}
}
