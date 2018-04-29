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
	const KMC_FIELD_TYPE_USER = 'userType';
	const KMC_FIELD_TYPE_METADATA_OBJECT = 'metadataObjectType';
	 
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
	public function applyCondition(IKalturaDbQuery $query, $xPaths = null)
	{
		if (!($query instanceof IKalturaIndexQuery))
			return;  
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
							case KalturaSearchConditionComparison::EQUAL:
								$comparison = ' = ';
								break;
							case KalturaSearchConditionComparison::GREATER_THAN:
								$comparison = ' > ';
								break;
							case KalturaSearchConditionComparison::GREATER_THAN_OR_EQUAL:
								$comparison = ' >= ';
								break;
							case KalturaSearchConditionComparison::LESS_THAN:
								$comparison = " < ";
								break;
							case KalturaSearchConditionComparison::LESS_THAN_OR_EQUAL:
								$comparison = " <= ";
								break;
							case KalturaSearchConditionComparison::NOT_EQUAL:
								$comparison = " <> ";
								break;
							default:
								KalturaLog::err("Missing comparison type");
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

						if ($xPaths[$field]->getType() == MetadataSearchFilter::KMC_FIELD_TYPE_DATE)
							$value = kTime::getRelativeTime($value);

						$newCondition = $metadataField . $comparison . $value;

						if ($item->getComparison() != KalturaSearchConditionComparison::EQUAL && $item->getComparison() != KalturaSearchConditionComparison::NOT_EQUAL)
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
						$matchType = $xPaths[$field]->getMatchType();
						
						// any value in the field
						if(trim($value) == '*')
						{
							$dataCondition = "{$pluginName}_{$fieldId}";
						}
						
						// exact match
						elseif (in_array($xPaths[$field]->getType(), array(self::KMC_FIELD_TYPE_OBJECT, self::KMC_FIELD_TYPE_USER, self::KMC_FIELD_TYPE_LIST)))
						{
							if ($item instanceof AdvancedSearchFilterMatchCondition && $item->not)
								$dataCondition = "!\"{$pluginName}_{$fieldId} $value " . kMetadataManager::SEARCH_TEXT_SUFFIX . "_{$fieldId}" . "\"";
							else
								$dataCondition = "\\\"{$pluginName}_{$fieldId} $value " . kMetadataManager::SEARCH_TEXT_SUFFIX . "_{$fieldId}" . "\\\"";
						}

						// anywhere in the field
						else 
						{
							if ($item instanceof AdvancedSearchFilterMatchCondition && $item->not)
								$dataCondition = "!\"{$pluginName}_{$fieldId} $value " . kMetadataManager::SEARCH_TEXT_SUFFIX . "_{$fieldId}\"";
							else
							{
								if($matchType == MetadataProfileFieldMatchType::EXACT)
								{
									$parsedFieldsValues = $xPaths[$field]->getParsedFieldValue($value);
									foreach ($parsedFieldsValues as $parsedFieldsValue)
									{
										$dataCondition .= "$parsedFieldsValue ";
									}
								}
								else
								{
									$dataCondition = "{$pluginName}_{$fieldId} << ( \"$value\" ) << " . kMetadataManager::SEARCH_TEXT_SUFFIX . "_{$fieldId}";
								}
							}
								
						}

						KalturaLog::debug("add $dataCondition");
						$dataConditions[] = "( $dataCondition )";
					}
					elseif($item instanceof MetadataSearchFilter)
					{
						$item->applyCondition($this, $xPaths);
					}
					elseif ($item instanceof DynamicObjectSearchFilter)
					{
						$item->applyCondition($this, $xPaths);
						$dataConditions = $item->matchClause;
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
			
			if(($this->type == self::SEARCH_OR) && ($this->condition))
				$this->condition = "( {$this->condition} )";
		}

		if($this->condition)
			$query->addMatch($this->condition);
			
		if (isset($this->orderBy))
		{
			$orderByField = substr($this->orderBy, 1);
			$orderBy = $this->orderBy[0] == '+' ? Criteria::ASC : Criteria::DESC;
			
			$metadataFieldType = null;
			$metadataField = $this->getMetadataSearchField($orderByField, array(), $metadataFieldType);
			$isIntVal = in_array($metadataFieldType, array(MetadataSearchFilter::KMC_FIELD_TYPE_DATE, MetadataSearchFilter::KMC_FIELD_TYPE_INT));
			if ($metadataField)
			{
				if ($isIntVal)
						$query->addNumericOrderBy($metadataField, $orderBy);
				else
						$query->addOrderBy($metadataField, $orderBy);
			}
		}
	}
	
	/**
	 * 
	 * @param string $field - xPath (metadataProfileField)
	 */
	protected function getMetadataSearchField($field = null, $xPaths = array(), &$fieldType = null){
		$fieldType = null;
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
		
		$fieldType = $xPaths[$field]->getType();
		switch ($xPaths[$field]->getType()){
			case MetadataSearchFilter::KMC_FIELD_TYPE_DATE:
			case MetadataSearchFilter::KMC_FIELD_TYPE_INT:
				$metadataField = MetadataPlugin::SPHINX_DYNAMIC_ATTRIBUTES . "." . MetadataPlugin::getSphinxFieldName($xPaths[$field]->getId());
				break;
			default:
				KalturaLog::ERR("Missing field type: ". $xPaths[$field]->getType());
				return null;
		}
		
		return $metadataField;
	}
	
	public static function createSphinxSearchCondition($partnerId, $text, $isIndex = false , $metadataProfileFieldId = null) {
		
		 if($isIndex) {
		 	$result = MetadataPlugin::PLUGIN_NAME . '_text' . ' ' . MetadataPlugin::PLUGIN_NAME . '_text_' . $partnerId
		 			. ' ' . $text . ' ' 
		 		. kMetadataManager::SEARCH_TEXT_SUFFIX . '_text_' . $partnerId . ' ' . kMetadataManager::SEARCH_TEXT_SUFFIX . '_text';
		 	return $result;
		 } 
		 
		 if(is_null($partnerId))
		 	return MetadataPlugin::PLUGIN_NAME . "_text << ( $text ) << " . kMetadataManager::SEARCH_TEXT_SUFFIX . '_text';
		else {
			if($metadataProfileFieldId)
				$result = MetadataPlugin::PLUGIN_NAME . "_{$metadataProfileFieldId} << ( $text ) << " . kMetadataManager::SEARCH_TEXT_SUFFIX . "_{$metadataProfileFieldId}";
			else
				$result = MetadataPlugin::PLUGIN_NAME . "_text_{$partnerId} << ( $text ) << " . kMetadataManager::SEARCH_TEXT_SUFFIX . "_text_{$partnerId}";

			return $result;
		}
	}
	
	protected function createSphinxSearchPhrase($partnerScope = null, $text, $metadataProfileFieldId = null) {
		
		$prefix = '@' . MetadataPlugin::getSphinxFieldName(MetadataPlugin::SPHINX_EXPANDER_FIELD_DATA) . ' ';
		
		if(is_null($partnerScope) || (count($partnerScope) != 1)) {
			return $prefix . MetadataSearchFilter::createSphinxSearchCondition(null, $text, false);
		}
		
		if($partnerScope == baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE) 
			$partnerScope = kCurrentContext::getCurrentPartnerId();
			
			
		return $prefix . MetadataSearchFilter::createSphinxSearchCondition($partnerScope, $text, false , $metadataProfileFieldId);
	}
	
	
	public function getFreeTextConditions($partnerScope, $freeTexts) 
	{
		$metadataProfileFieldIds = array();
		$metadataProfileId = $this->getMetadataProfileId();
		if ($metadataProfileId)
		{
			$metadataProfileFields = MetadataProfileFieldPeer::retrieveActiveByMetadataProfileId($metadataProfileId);
			foreach($metadataProfileFields as $metadataProfileField)
				$metadataProfileFieldIds[] = $metadataProfileField->getId();
		}

		KalturaLog::debug("freeText [$freeTexts]");
		$additionalConditions = array();
		
		if(preg_match('/^"[^"]+"$/', $freeTexts))
		{
			$freeText = str_replace('"', '', $freeTexts);
			$freeText = SphinxUtils::escapeString($freeText);
			$freeText = "^$freeText$";
			
			$additionalConditions[] = $this->createSphinxSearchPhrase($partnerScope, $freeTexts);
			
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
				$additionalConditions[] = $this->createSphinxSearchPhrase($partnerScope, $freeText);
				foreach($metadataProfileFieldIds as $metadataProfileFieldId)
					$additionalConditions[] = $this->createSphinxSearchPhrase($partnerScope, $freeText , $metadataProfileFieldId);
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
		$additionalConditions[] =  $this->createSphinxSearchPhrase($partnerScope, $freeTextExpr);
		foreach($metadataProfileFieldIds as $metadataProfileFieldId)
			$additionalConditions[] = $this->createSphinxSearchPhrase($partnerScope, $freeTextExpr , $metadataProfileFieldId);

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
	}
}
