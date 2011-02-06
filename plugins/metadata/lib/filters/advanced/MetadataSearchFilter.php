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
	const KMC_FIELD_TYPE_OBJECT = 'objectType';
	 
	/**
	 * @var string
	 */
	protected $condition = null;
	
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
	
	public function getCondition()
	{
		return $this->doGetCondition();
	}
	
	public function doGetCondition($xPaths = null)
	{
		if($this->condition)
			return $this->condition;
			
		if(is_null($xPaths))
		{
			$xPaths = array();
			$profileFields = MetadataProfileFieldPeer::retrieveActiveByMetadataProfileId($this->metadataProfileId);
			foreach($profileFields as $profileField)
				$xPaths[$profileField->getXpath()] = $profileField->getId();
		}
		
		$conditions = array();
		$pluginName = MetadataPlugin::PLUGIN_NAME;
		
		if(count($this->items))
		{
			foreach($this->items as $item)
			{
				$condition = null;
				
				if($item instanceof AdvancedSearchFilterCondition)
				{
					$field = $item->getField();
					if(isset($xPaths[$field]))
					{
						$value = $item->getValue();
						$value = SphinxUtils::escapeString($value);
						$fieldId = $xPaths[$field];
						$condition = "\"{$pluginName}_{$fieldId} $value " . kMetadataManager::SEARCH_TEXT_SUFFIX . "\"";
					}
				}
				elseif($item instanceof MetadataSearchFilter)
				{
					$condition = $item->doGetCondition($xPaths);
				}
					
				KalturaLog::debug("Append item [" . get_class($item) . "] condition [$condition]");
				
				if($condition)
					$conditions[] = "($condition)";
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
		
		if(preg_match('/^"[^"]+"$/', $freeTexts))
		{
			$freeText = str_replace('"', '', $freeTexts);
			$freeText = SphinxUtils::escapeString($freeText);
			$freeText = "^$freeText$";
			
//			$additionalConditions[] = "@(" . entryFilter::FREE_TEXT_FIELDS . ") $freeText";
			$additionalConditions[] = '@plugins_data ' . MetadataPlugin::PLUGIN_NAME . "_text << $freeTexts";
			
			return $additionalConditions;
		}
		
		if(strpos($freeTexts, baseObjectFilter::IN_SEPARATOR) > 0)
		{
			str_replace(baseObjectFilter::AND_SEPARATOR, baseObjectFilter::IN_SEPARATOR, $freeTexts);
		
			$freeTextsArr = explode(baseObjectFilter::IN_SEPARATOR, $freeTexts);
			foreach($freeTextsArr as $valIndex => $valValue)
				if(!is_numeric($valValue) && strlen($valValue) <= 1)
					unset($freeTextsArr[$valIndex]);
					
			foreach($freeTextsArr as $freeText)
			{
//				$additionalConditions[] = "@(" . entryFilter::FREE_TEXT_FIELDS . ") $freeText";
				$additionalConditions[] = '@plugins_data ' . MetadataPlugin::PLUGIN_NAME . "_text << $freeText";
			}
			return $additionalConditions;
		}
		
		$freeTextsArr = explode(baseObjectFilter::AND_SEPARATOR, $freeTexts);
		foreach($freeTextsArr as $valIndex => $valValue)
			if(!is_numeric($valValue) && strlen($valValue) <= 1)
				unset($freeTextsArr[$valIndex]);
				
		$freeTextExpr = implode(baseObjectFilter::AND_SEPARATOR, $freeTextsArr);
//		$additionalConditions[] = "@(" . entryFilter::FREE_TEXT_FIELDS . ") $freeTextExpr";
		$additionalConditions[] = '@plugins_data ' . MetadataPlugin::PLUGIN_NAME . "_text << $freeTextExpr";
		return $additionalConditions;
	}
	
	public function apply(baseObjectFilter $filter, Criteria &$criteria, array &$matchClause, array &$whereClause)
	{
		$condition = $this->getCondition();
		if($condition && strlen($condition))
			$matchClause[] = "@plugins_data $condition";
			
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
