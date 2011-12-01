<?php
/**
 * @package api
 * @subpackage filters
 */
class MetadataSearchFilter extends AdvancedSearchFilterOperator
{
	const KMC_FIELD_TYPE_TEXT = 'textType';
	const KMC_FIELD_TYPE_LIST = 'listType';
	const KMC_FIELD_TYPE_DATE = 'dateType';
	const KMC_FIELD_TYPE_INT = 'intType';
	const KMC_FIELD_TYPE_OBJECT = 'objectType';
	 
	/**
	 * @var string
	 */
	protected $condition = null;
	
	/**
	 * @var string
	 */
	protected $orderBy = null;
	
	/**
	 * @var int
	 */
	protected $metadataProfileId;
	
	public function setMetadataProfileId($metadataProfileId)
	{
		$this->metadataProfileId = $metadataProfileId;
	}
	
	public function getMetadataProfileId()
	{
		return $this->metadataProfileId;
	}
	
	public function setOrderBy($orderBy)
	{
		$this->orderBy = $orderBy;
	}
	
	public function getOrderBy()
	{
		return $this->orderBy;
	}
	
/*	public function getCondition()
	{
		throw new Exception("get condition is not in use!");
		return $this->doGetCondition();
	}
	*/
	public function applyCondition(array &$whereClause, array &$conditionClause, $xPaths = null)
	{		
		if($this->condition)
			return $this->condition;
			
		if(is_null($xPaths))
		{
			$xPaths = array();
			$profileFields = MetadataProfileFieldPeer::retrieveActiveByMetadataProfileId($this->metadataProfileId);
			foreach($profileFields as $profileField)
				$xPaths[$profileField->getXpath()] = $profileField;
		}

		$dataConditions = array();
		$subConditions = array();
		$pluginName = MetadataPlugin::PLUGIN_NAME;
		
		if(count($this->items))
		{
			foreach($this->items as $item)
			{
				$dataCondition = null;
				
				if ($item instanceof AdvancedSearchFilterComparableCondition){
					/* @var $item AdvancedSearchFilterComparableCondition  */
					$field = $item->getField();
					if(!isset($xPaths[$field])){
						$this->conditionClause[] = '1 <> 1';
						KalturaLog::ERR("Missing field: $field in xpath array: " . print_r($xPaths,true));
						continue;
					}
					
					switch ($item->getComparison()){
						case KalturaSearchConditionComparison::EQUEL:
							$comparison = ' = ';
							break;
						case KalturaSearchConditionComparison::GREATER_THAN:
							$comparison = ' > ';
							break;
						case KalturaSearchConditionComparison::GREATER_THAN_OR_EQUEL:
							$comparison = ' >= ';
							break;
						case KalturaSearchConditionComparison::LESS_THAN:
							$comparison = " < ";
							break;
						case KalturaSearchConditionComparison::LESS_THAN_OR_EQUEL:
							$comparison = " <= ";
							break;
						default:
							KalturaLog::ERR("Missing comparison type");
							continue;
					}
										
					$metadataField = $this->getMetadataSearchField($field, $xPaths);
					if (!$metadataField){
						KalturaLog::ERR("Missing metadataField for $field in xpath array: " . print_r($xPaths,true));
						continue;
					}
				
					if (!is_numeric($item->getValue()))
						$value = SphinxUtils::escapeString($item->getValue());
					else
						$value = $item->getValue();
						
					$newCondition = $metadataField . $comparison . $value;
					
					if ($item->getComparison() != KalturaSearchConditionComparison::EQUEL)
						$newCondition = "($newCondition AND $metadataField <> 0)";
						
					$this->conditionClause[] = $newCondition;
				}
				elseif ($item instanceof AdvancedSearchFilterCondition)
				{
					$field = $item->getField();
					if(!isset($xPaths[$field])){
						$this->conditionClause[] = '1 <> 1';
						KalturaLog::ERR("Missing field: $field in xpath array: " . print_r($xPaths,true));
						continue;
					}

					$value = $item->getValue();
					$value = SphinxUtils::escapeString($value);
					$fieldId = $xPaths[$field]->getId();
					if ($xPaths[$field]->getType() == self::KMC_FIELD_TYPE_LIST)
					{
						$dataCondition = "\"{$pluginName}_{$fieldId} $value " . kMetadataManager::SEARCH_TEXT_SUFFIX . "_{$fieldId}" . "\"";
					}
					else
					{
						$dataCondition = "{$pluginName}_{$fieldId} << $value << " . kMetadataManager::SEARCH_TEXT_SUFFIX . "_{$fieldId}";
					}
					
					kalturalog::debug("add $dataCondition");
					$dataConditions[] = "( $dataCondition )";
				}
				elseif($item instanceof MetadataSearchFilter)
				{
					$subConditions[] = $item->applyCondition($this->whereClause, $this->conditionClause, $xPaths);
				}					
			}
		}	
			
		if (count($dataConditions)){
			$metadataConditions = array();
			$glue = ($this->type == MetadataSearchFilter::SEARCH_AND ? ' ' : ' | ');
			$dataConditions = array_unique($dataConditions);
			$metadataConditions['@'. $this->getMetadataSearchField()] = implode($glue, $dataConditions);
			$this->condition = $this->appendToDataCondition($this->condition, $metadataConditions, $this->type);
		}
		
		if(count($subConditions)){
			kalturalog::debug("append conditions:". print_r($subConditions,true));
			foreach ($subConditions as $subCondition)
				$this->condition = $this->appendToDataCondition($this->condition, $subCondition, $this->type);
		}
		
		$conjuction = ' OR ';
		if ($this->type == self::SEARCH_AND)
			$conjuction = ' AND ';

		if (count($this->conditionClause))
			$conditionClause[] = '(' . implode($conjuction, $this->conditionClause) . ')';
		
		return $this->condition;
	}
	
	/**
	 * 
	 * @param string $field - xPath (metadataProfileField)
	 */
	public function getMetadataSearchField($field = null, $xPaths = array()){
		if(!$field)
			return MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPANDER_FIELD_DATA);
		
		if (!count($xPaths)){
			$profileFields = MetadataProfileFieldPeer::retrieveActiveByMetadataProfileId($this->metadataProfileId);
			foreach($profileFields as $profileField)
				$xPaths[$profileField->getXpath()] = $profileField;
		}

		if(!isset($xPaths[$field])){
			KalturaLog::ERR("Missing field: " . $field);
			return null;
		}
		
		if(is_null($xPaths[$field]->getSearchIndex())){
			KalturaLog::ERR("Missing field search index: " . $field);
			return null;
		}
			
			
		switch ($xPaths[$field]->getType()){
			case MetadataSearchFilter::KMC_FIELD_TYPE_DATE:
			case MetadataSearchFilter::KMC_FIELD_TYPE_INT:
				$metadataField = MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPENDER_FIELD_INT) . $xPaths[$field]->getSearchIndex();
				break;
			default:
				KalturaLog::ERR("Missing field type: ". $xPaths[$field]->getType());
				return null;
		}
		
		return $metadataField;
	}
	
	public function applyOrderBy(array &$orderByClause){
		KalturaLog::debug("apply order by: ". $this->orderBy);
		if (!isset($this->orderBy))
			return;
		
		$orderByField = substr($this->orderBy,1);
		$orderByAscending = $this->orderBy[0] == '+' ? true : false;
		
		$metadataField = $this->getMetadataSearchField($orderByField);
		if (!$metadataField)
			return;
		
		if ($orderByAscending){
			$orderByClause[] = $metadataField . ' ' . Criteria::ASC;
		}else{
			$orderByClause[] = $metadataField . ' ' . Criteria::DESC;
		}				
	}
	
	public function getFreeTextConditions($freeTexts)
	{
		KalturaLog::debug("freeText [$freeTexts]");
		$additionalConditions = array();
		
		if(preg_match('/^"[^"]+"$/', $freeTexts))
		{
			$freeText = str_replace('"', '', $freeTexts);
			$freeText = SphinxUtils::escapeString($freeText);
			$freeText = "^$freeText$";
			
			$additionalConditions[] = '@' . MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPANDER_FIELD_DATA) . ' ' . MetadataPlugin::PLUGIN_NAME . "_text << $freeTexts << " . kMetadataManager::SEARCH_TEXT_SUFFIX . '_text';
			
			return $additionalConditions;
		}
		
		if(strpos($freeTexts, baseObjectFilter::IN_SEPARATOR) > 0)
		{
			str_replace(baseObjectFilter::AND_SEPARATOR, baseObjectFilter::IN_SEPARATOR, $freeTexts);
		
			$freeTextsArr = explode(baseObjectFilter::IN_SEPARATOR, $freeTexts);
			foreach($freeTextsArr as $valIndex => $valValue)
				if(!is_numeric($valValue) && strlen($valValue) <= 0)
					unset($freeTextsArr[$valIndex]);
					
			foreach($freeTextsArr as $freeText)
				$additionalConditions[] = '@' . MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPANDER_FIELD_DATA) . ' ' . MetadataPlugin::PLUGIN_NAME . "_text << $freeText << " . kMetadataManager::SEARCH_TEXT_SUFFIX . '_text';
			
			return $additionalConditions;
		}
		
		$freeTextsArr = explode(baseObjectFilter::AND_SEPARATOR, $freeTexts);
		foreach($freeTextsArr as $valIndex => $valValue)
			if(!is_numeric($valValue) && strlen($valValue) <= 0)
				unset($freeTextsArr[$valIndex]);
				
		$freeTextsArr = array_unique($freeTextsArr);
		$freeTextExpr = implode(baseObjectFilter::AND_SEPARATOR, $freeTextsArr);
		$additionalConditions[] = '@'. MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPANDER_FIELD_DATA) . ' ' . MetadataPlugin::PLUGIN_NAME . "_text << $freeTextExpr << " . kMetadataManager::SEARCH_TEXT_SUFFIX . '_text';
		return $additionalConditions;
	}
	
	
	public function apply(baseObjectFilter $filter, Criteria &$criteria, array &$matchClause, array &$whereClause, array &$conditionClause, array &$orderByClause)
	{
		KalturaLog::debug("matchClause [". print_r($matchClause,true) ."] whereClause [ " . print_r($whereClause,true) . "] conditionClause [" . print_r($conditionClause,true) ."] orderByClause [" . print_r($orderByClause,true) . "]");
		$this->applyOrderBy($orderByClause);
		$conditions = $this->applyCondition($whereClause, $conditionClause);
		KalturaLog::debug("apply matchClause: ". $conditions);

		if (count($conditions))
			foreach ($conditions as $key => $value)
				$matchClause[] = $key . ' ' . $value;
		
		
//		$filter->unsetByName('_free_text');
	}
	
	public function addToXml(SimpleXMLElement &$xmlElement)
	{
		parent::addToXml($xmlElement);
		
		$xmlElement->addAttribute('metadataProfileId', $this->metadataProfileId);
	}
	
	public function fillObjectFromXml(SimpleXMLElement $xmlElement)
	{
		parent::fillObjectFromXml($xmlElement);
		
//		KalturaLog::debug('Fill object from XML [' . $xmlElement->asXML() . ']');
		
		$attr = $xmlElement->attributes();
		if(isset($attr['metadataProfileId']))
			$this->metadataProfileId = (int) $attr['metadataProfileId'];
			
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
