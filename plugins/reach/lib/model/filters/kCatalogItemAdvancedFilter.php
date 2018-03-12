<?php
/**
 * @package plugins.viewHistory
 * @subpackage model.filters
 */
class kCatalogItemAdvancedFilter extends AdvancedSearchFilterItem
{
	/**
	 * @var int
	 */
	public $serviceTypeEqual;
	
	/**
	 * @var string
	 */
	public $serviceTypeIn;
	
	/**
	 * @var int
	 */
	public $serviceFeatureEqual;
	
	/**
	 * @var string
	 */
	public $serviceFeatureIn;
	
	/**
	 * @var int
	 */
	public $turnAroundTimeEqual;
	
	/**
	 * @var string
	 */
	public $turnAroundTimeIn;
	
	/**
	 * @var string
	 */
	public $sourceLanguageEqual;
	
	// Class Getters
	public function getServiceTypeEqual() 		{ return $this->serviceTypeEqual; 	}
	public function getServiceTypeIn() 			{ return $this->serviceTypeIn; 		}
	public function getServiceFeatureEqual()	{ return $this->serviceFeatureEqual; }
	public function getServiceFeatureIn()		{ return $this->serviceFeatureIn; }
	public function getTurnAroundTimeEqual()	{ return $this->turnAroundTimeEqual; }
	public function getTurnAroundTimeIn()		{ return $this->turnAroundTimeIn; }
	public function getSourceLanguageEqual()	{ return $this->sourceLanguageEqual; }
	
	// Class Setters
	public function setServiceTypeEqual($v)		{ $this->serviceTypeEqual = $v; }
	public function setServiceTypeIn($v)		{ $this->serviceTypeIn = $v; }
	public function setServiceFeatureEqual($v)	{ $this->serviceFeatureEqual = $v; }
	public function setServiceFeatureIn($v)		{ $this->serviceFeatureIn = $v; }
	public function setTurnAroundTimeEqual($v)	{ $this->turnAroundTimeEqual = $v; }
	public function setTurnAroundTimeIn($v)		{ $this->turnAroundTimeIn = $v; }
	public function setSourceLanguageEqual($v)	{ $this->sourceLanguageEqual = $v; }
	
	
	// Internal functions
	
	/* (non-PHPdoc)
 	 * @see AdvancedSearchFilterItem::applyCondition()
 	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
		$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
		
		if($this->getServiceTypeEqual())
		{
			$condition = ReachPlugin::CATALOG_ITEM_INDEX_PREFIX . $partnerId;
			$condition .= " " . ReachPlugin::CATALOG_ITEM_INDEX_SERVICE_TYPE . $this->getServiceTypeEqual();
			$condition .= " " . ReachPlugin::CATALOG_ITEM_INDEX_SUFFIX . $partnerId;
			
			$query->addMatch('@' . ReachPlugin::getSearchFieldName(ReachPlugin::SEARCH_FIELD_CATALOG_ITEM_DATA) . " " . $condition);
		}
		
		if($this->getServiceTypeIn())
		{
			$serviceTypesStrs = array();
			$serviceTypes = explode(",", $this->getServiceTypeIn());
			foreach($serviceTypes as $serviceType)
			{
				if(!$serviceType)
					continue;
				
				$serviceTypesStrs[] = ReachPlugin::CATALOG_ITEM_INDEX_SERVICE_TYPE . $serviceType;
			}
			
			$condition = ReachPlugin::CATALOG_ITEM_INDEX_PREFIX . $partnerId;
			$condition .= ' (' . implode(' | ', $serviceTypesStrs) . ')';
			$condition .= " " . ReachPlugin::CATALOG_ITEM_INDEX_SUFFIX . $partnerId;
			
			$query->addMatch('@' . ReachPlugin::getSearchFieldName(ReachPlugin::SEARCH_FIELD_CATALOG_ITEM_DATA) . " " . $condition);
		}
		
		if($this->getServiceFeatureEqual())
		{
			$condition = ReachPlugin::CATALOG_ITEM_INDEX_PREFIX . $partnerId;
			$condition .= " " . ReachPlugin::CATALOG_ITEM_INDEX_SERVICE_FEATURE . $this->getServiceFeatureEqual();
			$condition .= " " . ReachPlugin::CATALOG_ITEM_INDEX_SUFFIX . $partnerId;
			
			$query->addMatch('@' . ReachPlugin::getSearchFieldName(ReachPlugin::SEARCH_FIELD_CATALOG_ITEM_DATA) . " " . $condition);
		}
		
		if($this->getServiceFeatureIn())
		{
			$serviceFeatureStrs = array();
			$serviceFeatures = explode(",", $this->getServiceFeatureIn());
			foreach($serviceFeatures as $serviceFeature)
			{
				if(!$serviceFeature)
					continue;
				
				$serviceFeatureStrs[] = ReachPlugin::CATALOG_ITEM_INDEX_SERVICE_FEATURE . $serviceFeature;
			}
			
			$condition = ReachPlugin::CATALOG_ITEM_INDEX_PREFIX . $partnerId;
			$condition .= ' (' . implode(' | ', $serviceFeatureStrs) . ')';
			$condition .= " " . ReachPlugin::CATALOG_ITEM_INDEX_SUFFIX . $partnerId;
			
			$query->addMatch('@' . ReachPlugin::getSearchFieldName(ReachPlugin::SEARCH_FIELD_CATALOG_ITEM_DATA) . " " . $condition);
		}
		
		if($this->getTurnAroundTimeEqual())
		{
			$condition = ReachPlugin::CATALOG_ITEM_INDEX_PREFIX . $partnerId;
			$condition .= " " . ReachPlugin::CATALOG_ITEM_INDEX_TURN_AROUND_TIME . $this->getTurnAroundTimeEqual();
			$condition .= " " . ReachPlugin::CATALOG_ITEM_INDEX_SUFFIX . $partnerId;
			
			$query->addMatch('@' . ReachPlugin::getSearchFieldName(ReachPlugin::SEARCH_FIELD_CATALOG_ITEM_DATA) . " " . $condition);
		}
		
		if($this->getTurnAroundTimeIn())
		{
			$turnAroundsStrs = array();
			$turnAroundTimes = explode(",", $this->turnAroundTimeIn);
			foreach($turnAroundTimes as $turnAroundTime)
			{
				if(!$turnAroundTime)
					continue;
				
				$turnAroundsStrs[] = ReachPlugin::CATALOG_ITEM_INDEX_TURN_AROUND_TIME . $turnAroundTime;
			}
			
			$condition = ReachPlugin::CATALOG_ITEM_INDEX_PREFIX . $partnerId;
			$condition .= ' (' . implode(' | ', $turnAroundsStrs) . ')';
			$condition .= " " . ReachPlugin::CATALOG_ITEM_INDEX_SUFFIX . $partnerId;
			
			$query->addMatch('@' . ReachPlugin::getSearchFieldName(ReachPlugin::SEARCH_FIELD_CATALOG_ITEM_DATA) . " " . $condition);
		}
		
		if($this->getSourceLanguageEqual())
		{
			$condition = ReachPlugin::CATALOG_ITEM_INDEX_PREFIX . $partnerId;
			$condition .= " " . ReachPlugin::CATALOG_ITEM_INDEX_LANGUAGE . $this->getSourceLanguageEqual();
			$condition .= " " . ReachPlugin::CATALOG_ITEM_INDEX_SUFFIX . $partnerId;
			
			$query->addMatch('@' . ReachPlugin::getSearchFieldName(ReachPlugin::SEARCH_FIELD_CATALOG_ITEM_DATA) . " " . $condition);
		}
	}
	
	private function formatMatchPhrase($conditionString)
	{
		if(!strlen($conditionString))
			return null;
		
		if(preg_match('/[\s\t]/', $conditionString))
			$res = '"' . SphinxUtils::escapeString($conditionString) . '"';
		else
			$res = SphinxUtils::escapeString($conditionString);
		
		return $res;
	}
	
	public function getFreeTextConditions($partnerScope, $freeTexts)
	{
		$additionalConditions = array();
		
		if($this->getCuePointsFreeText())
			return;
		
		if(preg_match('/^"[^"]+"$/', $freeTexts))
		{
			$freeText = str_replace('"', '', $freeTexts);
			$freeText = SphinxUtils::escapeString($freeText);
			$freeText = "^$freeText$";
			
			$additionalConditions[] = $this->createSphinxMatchPhrase($freeText);
			
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
				$additionalConditions[] = $this->createSphinxMatchPhrase($freeText);
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
		$additionalConditions[] =  $this->createSphinxMatchPhrase($freeTextExpr);
		return $additionalConditions;
	}
}
