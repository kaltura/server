<?php
/**
 * @package plugins.cuePoint
 * @subpackage model.filters
 */
class EntryCuePointSearchFilter extends AdvancedSearchFilterItem
{
	/**
	 * @var string
	 */
	protected  $cuePointsFreeText;
	
	/**
	 * @dynamicType KalturaCuePointType
	 * @var string
	 */
	protected  $cuePointTypeIn;
	
	/**
	 * @var int
	 */
	protected $cuePointSubTypeEqual;
	
	/**
	 * @return the $freeText
	 */
	public function getCuePointsFreeText() {
		return $this->$cuePointsFreeText;
	}

	/**
	 * @param string $freeText
	 */
	public function setCuePointsFreeText($freeText) {
		$this->$cuePointsFreeText = $this->formatMatchPhrase($freeText);
	}
	
	/**
	 * @return the $cuePointTypeIn
	 */
	public function getCuePointTypeIn() {
		return $this->cuePointTypeIn;
	}

	/**
	 * @param string $cuePointTypeIn
	 */
	public function setCuePointTypeIn($cuePointTypeIn) {
		$this->cuePointTypeIn = $cuePointTypeIn;
	}
	
	/**
	 * @return the $cuePointTypeIn
	 */
	public function getCuePointSubTypeEqual() {
		return $this->cuePointSubTypeEqual;
	}

	/**
	 * @param string $cuePointTypeIn
	 */
	public function setCuePointSubTypeEqual($cuePointSubTypeEqual) {
		$this->cuePointSubTypeEqual = $cuePointSubTypeEqual;
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
	
	private function applyMatchCondition($conditionStr, IKalturaIndexQuery $query)
	{			
		$matchCondition = $this->createSphinxMatchPhrase($conditionStr); 
		
		$query->addMatch("($matchCondition)");			
	}
	
	private function createSphinxMatchPhrase($conditionStr) 
	{	
		if(isset($this->cuePointTypeIn))
		{
			$conditions = array();
			
			$types = explode(',', $this->cuePointTypeIn);
		
			foreach($types as $type)
			{				
				$partnerId = kCurrentContext::$partner_id ? kCurrentContext::$partner_id : kCurrentContext::$ks_partner_id;
				
				$prefix = CuePointPlugin::ENTRY_CUE_POINT_INDEX_PREFIX . $partnerId . "_" . $type;
				
				$cuePointSubType = $this->getCuePointSubTypeEqual();
				if($cuePointSubType)
					$prefix .= " " . CuePointPlugin::ENTRY_CUE_POINT_INDEX_SUB_TYPE . $cuePointSubType;
				
				$postfix = CuePointPlugin::ENTRY_CUE_POINT_INDEX_SUFFIX . $partnerId . "_" . $type;

				$conditions[] = "(" . $prefix . " << " . $conditionStr . " << " . $postfix .")";
			}
			
			$condition = implode(' | ', $conditions);
		}
		else {
			$condition = "(cuePoint << $conditionStr << cpend)";
		}
		
		KalturaLog::debug("condition [" . print_r($condition, true) . "]");
		
		return '@' . CuePointPlugin::getSearchFieldName(CuePointPlugin::SEARCH_FIELD_DATA) . " " . $condition;
	}
	
	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::applyCondition()
	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
		if ($query instanceof IKalturaIndexQuery){
			$this->applyMatchCondition($this->getCuePointsFreeText(), $query);
		}
	}
	
	public function getFreeTextConditions($partnerScope, $freeTexts) 
	{
		KalturaLog::debug("freeText [$freeTexts]");
		$additionalConditions = array();
		
		if($this->getCuePointsFreeText())
			return;
		
		if(preg_match('/^"[^"]+"$/', $freeTexts))
		{
			$freeText = str_replace('"', '', $freeTexts);
			$freeText = SphinxUtils::escapeString($freeText);
			$freeText = "^$freeText$";
			
			$additionalConditions[] = $this->createSphinxMatchPhrase($freeTexts);
			
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
