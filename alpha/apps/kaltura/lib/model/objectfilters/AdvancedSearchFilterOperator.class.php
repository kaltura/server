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
				if($item instanceof AdvancedSearchFilterOperator)
				{
					$condition = $item->getCondition();
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
	
	public function apply(baseObjectFilter $filter, Criteria &$criteria, array &$matchClause, array &$whereClause)
	{
		KalturaLog::debug("apply from [" . get_class($filter) . "]");
		
		$condition = $this->getCondition();
		if($condition && strlen($condition))
			$matchClause[] = $condition;
	}
	
	public function addToXml(SimpleXMLElement &$xmlElement)
	{
		parent::addToXml($xmlElement);
		
		$xmlElement->addAttribute('operatorType', $this->type);
		
		foreach($this->items as $item)
		{
			$itemXmlElement = $xmlElement->addChild('item');
			$itemXmlElement->addAttribute('type', get_class($item));
			
			$item->addToXml($itemXmlElement);
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
