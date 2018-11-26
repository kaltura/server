<?php
/**
 * @package Core
 * @subpackage model.filters.advanced
 */ 
class AdvancedSearchFilterOperator extends AdvancedSearchFilterItem implements IKalturaIndexQuery
{
	const SEARCH_AND = 1;
	const SEARCH_OR = 2;
	
	/**
	 * @var IKalturaIndexQuery
	 */
	protected $parentQuery;
	
	/**
	 * @var int AND or OR
	 */
	protected $type;
	
	/**
	 * @var array
	 */
	protected $items;
	
	/**
	 * local whereClause
	 * @var array
	 */
	protected $whereClause = array();
	
	/**
	 * local $matchClause
	 * @var array
	 */
	protected $matchClause = array();
	
	/**
	 * local conditionClause
	 * @var array
	 */
	protected $conditionClause = array();

	/**
	 * @var string
	 */
	protected $condition = null;
	
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
	
	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::applyCondition()
	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
		$this->parentQuery = $query;
		
		if(!$this->condition)
		{
			if($this->items && count($this->items))
			{
				$queryDestination = $this;
				if($this->type == self::SEARCH_AND)
					$queryDestination = $query;
					
				foreach($this->items as $item)
				{
					KalturaLog::debug("item type: " . get_class($item));
					if($item instanceof AdvancedSearchFilterItem)
					{
						$item->applyCondition($queryDestination);
					}
				}
				
				if($this->type == self::SEARCH_OR && count($this->matchClause))
				{
					$matchClause = array_unique($this->matchClause);
					$this->condition = '( ' . implode(' | ', $matchClause) . ' )';
				}
			}
		}
	
		if($this->condition && $query instanceof IKalturaIndexQuery)
			$query->addMatch($this->condition);
	}
	
	public function getFreeTextConditions($partnerScope, $freeTexts)
	{
		$additionalConditions = array();
		if($this->items && count($this->items))
		{
			foreach($this->items as $item)
			{
				if($item instanceof AdvancedSearchFilterItem)
				{
					$itemAdditionalConditions = $item->getFreeTextConditions($partnerScope, $freeTexts);
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
	
	/* (non-PHPdoc)
	 * @see IKalturaDbQuery::addColumnWhere()
	 */
	public function addColumnWhere($column, $value, $comparison)
	{
		if($this->parentQuery)
			$this->parentQuery->addColumnWhere($column, $value, $comparison);
	}
	
	/* (non-PHPdoc)
	 * @see IKalturaIndexQuery::addWhere()
	 */
	public function addWhere($statement)
	{
		$this->whereClause[] = $statement;
	}

	/* (non-PHPdoc)
	 * @see IKalturaIndexQuery::addMatch()
	 */
	public function addMatch($match)
	{
		$this->matchClause[] = $match;
	}

	/* (non-PHPdoc)
	 * @see IKalturaIndexQuery::addCondition()
	 */
	public function addCondition($condition)
	{
		if($this->parentQuery && $this->parentQuery instanceof IKalturaIndexQuery)
			$this->parentQuery->addCondition($condition);
	}

	/* (non-PHPdoc)
	 * @see IKalturaIndexQuery::addOrderBy()
	 */
	public function addOrderBy($column, $orderByType = Criteria::ASC)
	{
		if($this->parentQuery)
			$this->parentQuery->addOrderBy($column, $orderByType);
	}
	
	public function addNumericOrderBy($column, $orderByType = Criteria::ASC)
	{
		if($this->parentQuery)
			$this->parentQuery->addNumericOrderBy($column, $orderByType);
	}
}
