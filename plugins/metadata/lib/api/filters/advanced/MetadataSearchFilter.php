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
	
	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterOperator::applyCondition()
	 */
	public function applyCondition(IKalturaIndexQuery $query, $xPaths = null)
	{
		$this->parentQuery = $query;
		
		if(!$this->condition)
		{
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
							$this->addCondition('1 <> 1');
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
					
						$value = $item->getValue();
						if (!is_numeric($value))
						{
							switch($value)
							{
								case Criteria::CURRENT_DATE:
									$d = getdate();
									$value = mktime(0, 0, 0, $d['mon'], $d['mday'], $d['year']);
									break;
	
								case Criteria::CURRENT_TIME:
								case Criteria::CURRENT_TIMESTAMP:
									$value = time();
									break;
									
								default:
									if ($xPaths[$field]->getType() == MetadataSearchFilter::KMC_FIELD_TYPE_DATE || 
										$xPaths[$field]->getType() == MetadataSearchFilter::KMC_FIELD_TYPE_INT)
									{
										$this->addCondition('1 <> 1');
										KalturaLog::ERR("wrong search value: $field is numeric. search value: " . print_r($item->getValue(),true));
										continue;
									}
									
									$value = SphinxUtils::escapeString($value);
									break;
							}
						}
							
						$newCondition = $metadataField . $comparison . $value;
						
						if ($item->getComparison() != KalturaSearchConditionComparison::EQUEL)
							$newCondition = "($newCondition AND $metadataField <> 0)";
							
						$this->addCondition($newCondition);
					}
					elseif ($item instanceof AdvancedSearchFilterCondition)
					{
						$field = $item->getField();
						if(!isset($xPaths[$field])){
							$this->addCondition('1 <> 1');
							KalturaLog::ERR("Missing field: $field in xpath array: " . print_r($xPaths,true));
							continue;
						}
	
						$value = $item->getValue();
						$value = SphinxUtils::escapeString($value);
						$fieldId = $xPaths[$field]->getId();
						
						// any value in the field
						if(trim($value) == '*')
						{
							$dataCondition = "{$pluginName}_{$fieldId}";
						}
						
						// exact match
						elseif ($xPaths[$field]->getType() == self::KMC_FIELD_TYPE_LIST)
						{
							$dataCondition = "\\\"{$pluginName}_{$fieldId} $value " . kMetadataManager::SEARCH_TEXT_SUFFIX . "_{$fieldId}" . "\\\"";
						}
						
						// anywhere in the field
						else 
						{
							$dataCondition = "{$pluginName}_{$fieldId} << ( $value ) << " . kMetadataManager::SEARCH_TEXT_SUFFIX . "_{$fieldId}";
						}
						
						kalturalog::debug("add $dataCondition");
						$dataConditions[] = "( $dataCondition )";
					}
					elseif($item instanceof MetadataSearchFilter)
					{
						$item->applyCondition($this, $xPaths);
					}					
				}
			}	
				
			if (count($dataConditions))
			{
				$glue = ($this->type == MetadataSearchFilter::SEARCH_AND ? ' ' : ' | ');
				$dataConditions = array_unique($dataConditions);
				$key = '@'. $this->getMetadataSearchField();
				$value = implode($glue, $dataConditions);
				$this->addMatch("$key $value");
			}
			
			$matchClause = array_unique($this->matchClause);
			$glue = $this->type == self::SEARCH_AND ? ' ' : ' | ';
			$this->condition = implode($glue, $matchClause);
			
			if($this->type == self::SEARCH_OR)
				$this->condition = "( {$this->condition} )";
		}

		if($this->condition)
			$query->addMatch($this->condition);
			
		if (isset($this->orderBy))
		{
			$orderByField = substr($this->orderBy, 1);
			$orderByAscending = $this->orderBy[0] == '+' ? true : false;
			
			$metadataField = $this->getMetadataSearchField($orderByField);
			if ($metadataField)
			{
				if ($orderByAscending)
					$query->addOrderBy($metadataField, Criteria::ASC);
				else
					$query->addOrderBy($metadataField, Criteria::DESC);
			}
		}
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
	
	protected function createSphinxExpanderCond($partnerScope = null, $text) {
		
		if(is_null($partnerScope) || (count($partnerScope) != 1)) {
			return '@' . MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPANDER_FIELD_DATA) . ' ' . MetadataPlugin::PLUGIN_NAME .
			"_text << ( $text ) << " . kMetadataManager::SEARCH_TEXT_SUFFIX . '_text';
		}
		
		if($partnerScope == baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE) 
			$partnerScope = kCurrentContext::getCurrentPartnerId();
			
			
		return '@' . MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPANDER_FIELD_DATA) . ' ' . MetadataPlugin::PLUGIN_NAME . 
			"_text_{$partnerScope} << ( $text ) << " . kMetadataManager::SEARCH_TEXT_SUFFIX . "_text_{$partnerScope}";
	}
	
	
	public function getFreeTextConditions($partnerScope, $freeTexts) 
	{
		KalturaLog::debug("freeText [$freeTexts]");
		$additionalConditions = array();
		
		if(preg_match('/^"[^"]+"$/', $freeTexts))
		{
			$freeText = str_replace('"', '', $freeTexts);
			$freeText = SphinxUtils::escapeString($freeText);
			$freeText = "^$freeText$";
			
			$additionalConditions[] = $this->createSphinxExpanderCond($partnerScope, $freeTexts);
			
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
			{
				$freeText = SphinxUtils::escapeString($freeText);
				$additionalConditions[] = $this->createSphinxExpanderCond($partnerScope, $freeText);
			}
			
			return $additionalConditions;
		}
		
		$freeTextsArr = explode(baseObjectFilter::AND_SEPARATOR, $freeTexts);
		foreach($freeTextsArr as $valIndex => $valValue)
			if(!is_numeric($valValue) && strlen($valValue) <= 0)
				unset($freeTextsArr[$valIndex]);
				
		$freeTextsArr = array_unique($freeTextsArr);
		$freeTextExpr = implode(baseObjectFilter::AND_SEPARATOR, $freeTextsArr);
		$freeTextExpr = SphinxUtils::escapeString($freeTextExpr);
		$additionalConditions[] =  $this->createSphinxExpanderCond($partnerScope, $freeTextExpr);
		return $additionalConditions;
	}
	
	public function addToXml(SimpleXMLElement &$xmlElement)
	{
		parent::addToXml($xmlElement);
		
		$xmlElement->addAttribute('metadataProfileId', $this->metadataProfileId);

		if (!is_null($this->orderBy))
			$xmlElement->addAttribute('orderBy', $this->orderBy);
	}
	
	public function fillObjectFromXml(SimpleXMLElement $xmlElement)
	{
		parent::fillObjectFromXml($xmlElement);
		
//		KalturaLog::debug('Fill object from XML [' . $xmlElement->asXML() . ']');
		
		$attr = $xmlElement->attributes();
		if(isset($attr['metadataProfileId']))
			$this->metadataProfileId = (int) $attr['metadataProfileId'];

		if(isset($attr['orderBy']))
			$this->orderBy = (string) $attr['orderBy'];
		
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
