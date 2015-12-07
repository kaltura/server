<?php
/**
 * @package plugins.caption
 * @subpackage model.filters
 */
class EntryCaptionAssetSearchFilter extends AdvancedSearchFilterItem
{
	/**
	 * @var string
	 */
	protected  $contentLike;

	/**
	 * @var string
	 */
	protected $contentMultiLikeOr;

	/**
	 * @var string
	 */
	protected $contentMultiLikeAnd;
	
	
	/**
	 * @return the $contentLike
	 */
	public function getContentLike() {
		return $this->contentLike;
	}

	/**
	 * @return the $contentMultiLikeOr
	 */
	public function getContentMultiLikeOr() {
		return $this->contentMultiLikeOr;
	}

	/**
	 * @return the $contentMultiLikeAnd
	 */
	public function getContentMultiLikeAnd() {
		return $this->contentMultiLikeAnd;
	}

	/**
	 * @param string $contentLike
	 */
	public function setContentLike($contentLike) {
		$this->contentLike = $this->formatCondition($contentLike, null, ' ');
	}

	/**
	 * @param string $contentMultiLikeOr
	 */
	public function setContentMultiLikeOr($contentMultiLikeOr) {
		$this->contentMultiLikeOr = $this->formatCondition($contentMultiLikeOr, ',', ' | ');
	}

	/**
	 * @param string $contentMultiLikeAnd
	 */
	public function setContentMultiLikeAnd($contentMultiLikeAnd) {
		$this->contentMultiLikeAnd = $this->formatCondition($contentMultiLikeAnd, ',', ' ');
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
		if(!is_null($conditionStr))
		{
			$condition = "ca_prefix<<$conditionStr<<ca_sufix";
			KalturaLog::debug("condition [" . print_r($condition, true) . "]");
			$key = '@' . CaptionSearchPlugin::getSearchFieldName(CaptionSearchPlugin::SEARCH_FIELD_DATA);
			$query->addMatch("($key $condition)");			
		}
	}
	
	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::applyCondition()
	 */
	public function applyCondition(IKalturaDbQuery $query)
	{
		if ($query instanceof IKalturaIndexQuery){
			$this->addCondition($this->getContentLike(), $query);
			$this->addCondition($this->getContentMultiLikeAnd(), $query);
			$this->addCondition($this->getContentMultiLikeOr(), $query);
		}
	}
	
	public function addToXml(SimpleXMLElement &$xmlElement)
	{
		parent::addToXml($xmlElement);
		
		$xmlElement->addAttribute('contentLike', $this->getContentLike());
		$xmlElement->addAttribute('contentMultiLikeAnd', $this->getContentMultiLikeAnd());
		$xmlElement->addAttribute('contentMultiLikeOr', $this->getContentMultiLikeOr());
	}
	
	public function fillObjectFromXml(SimpleXMLElement $xmlElement)
	{
		parent::fillObjectFromXml($xmlElement);
		
		$attr = $xmlElement->attributes();
		if(isset($attr['contentLike']) && strlen($attr['contentLike']))
			$this->setContentLike($attr['contentLike']);
		if(isset($attr['contentMultiLikeAnd']) && strlen($attr['contentMultiLikeAnd']))
			$this->setContentMultiLikeAnd($attr['contentMultiLikeAnd']);
		if(isset($attr['contentMultiLikeOr']) && strlen($attr['contentMultiLikeOr']))
			$this->setContentMultiLikeOr($attr['contentMultiLikeOr']);
	}
}
