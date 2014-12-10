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
	protected  $freeText;
	
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
	public function getFreeText() {
		return $this->freeText;
	}

	/**
	 * @param string $freeText
	 */
	public function setFreeText($freeText) {
		$this->freeText = $this->formatCondition($freeText, null, ' ');
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

	private function formatCondition($conditionString, $explodeDelimiter, $implodeDelimiter)
	{
		if(!strlen($conditionString))
		{
			return null;
		}
		
		$res = null;
		if($explodeDelimiter)
		{		
			$vals = explode($explodeDelimiter, $conditionString);
			foreach($vals as $valIndex => $valValue)
			{
				if(!$valValue)
					unset($vals[$valIndex]);
				elseif(preg_match('/[\s\t]/', $valValue))
					$vals[$valIndex] = '"' . SphinxUtils::escapeString($valValue) . '"';
				else
					$vals[$valIndex] = SphinxUtils::escapeString($valValue);
			}
						
			if(count($vals))
			{
				$res = implode($implodeDelimiter, $vals);
			}	
		}	
		else
		{
			if(preg_match('/[\s\t]/', $conditionString)) 
				$res = '"' . SphinxUtils::escapeString($conditionString) . '"';
			else
				$res = SphinxUtils::escapeString($conditionString);
									
		}
		return $res;
	}
	
	private function addCondition($conditionStr, IKalturaIndexQuery $query)
	{	
		$conditions = array();
		
		if(isset($this->cuePointTypeIn))
		{
			$types = explode(',', $this->cuePointTypeIn);
		
			foreach($types as $type)
			{
				$cuePointSubType = $this->getCuePointSubTypeEqual();
				
				$prefix = $cuePointSubType ?  "cp_" . $type . "_" . $cuePointSubType . " << " : "cp_" . $type . " << ";
				$postfix = $cuePointSubType ? " << cp_" . $type . "_" . $this->getCuePointSubTypeEqual() : " << cp_" . $type;

				$conditions[] = "(" . $prefix . $conditionStr . $postfix .")";
			}
			
			$condition = implode(' | ', $conditions);
		}
		else {
			$condition = "(cuePoint << $conditionStr << cpend)";
		}
		
		KalturaLog::debug("condition [" . print_r($condition, true) . "]");
		
		$key = '@' . CuePointPlugin::getSearchFieldName(CuePointPlugin::SEARCH_FIELD_DATA);
		$query->addMatch("($key $condition)");			
	}
	
	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::applyCondition()
	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
		if ($query instanceof IKalturaIndexQuery){
			$this->addCondition($this->getFreeText(), $query);
		}
	}
}
