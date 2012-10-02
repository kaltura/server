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
	protected $condition = null;
	
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
		$this->contentLike = $contentLike;
	}

	/**
	 * @param string $contentMultiLikeOr
	 */
	public function setContentMultiLikeOr($contentMultiLikeOr) {
		$this->contentMultiLikeOr = $contentMultiLikeOr;
	}

	/**
	 * @param string $contentMultiLikeAnd
	 */
	public function setContentMultiLikeAnd($contentMultiLikeAnd) {
		$this->contentMultiLikeAnd = $contentMultiLikeAnd;
	}

	public function getCondition()
	{
		if($this->condition)
			return $this->condition;
			
		$conditions = array();
		
		if(!is_null($this->contentLike))
			$conditions[] = $this->contentLike;
		
		if(!is_null($this->contentMultiLikeAnd))
			$conditions[] = $this->contentMultiLikeAnd;
		
		if(!is_null($this->contentMultiLikeOr))
			$conditions[] = $this->contentMultiLikeOr ;
			
		if(!count($conditions))
			return null;
			
		$this->condition = implode(' ', $conditions);
		$this->condition = "ca_prefix<<$this->condition<<ca_sufix";
		return $this->condition;
	}
	
	/* (non-PHPdoc)
	 * @see AdvancedSearchFilterItem::applyCondition()
	 */
	public function applyCondition(IKalturaIndexQuery $query)
	{
		$condition = $this->getCondition();
		KalturaLog::debug("condition [" . print_r($condition, true) . "]");
		$key = '@' . CaptionSearchPlugin::getSearchFieldName(CaptionSearchPlugin::SEARCH_FIELD_DATA);
		$query->addMatch("($key $condition)");
	}
	
	public function addToXml(SimpleXMLElement &$xmlElement)
	{
		parent::addToXml($xmlElement);
		
		$xmlElement->addAttribute('contentLike', $this->contentLike);
		$xmlElement->addAttribute('contentMultiLikeAnd', $this->contentMultiLikeAnd);
		$xmlElement->addAttribute('contentMultiLikeOr', $this->contentMultiLikeOr);
	}
	
	public function fillObjectFromXml(SimpleXMLElement $xmlElement)
	{
		parent::fillObjectFromXml($xmlElement);
		
		$attr = $xmlElement->attributes();
		if(isset($attr['contentLike']) && strlen($attr['contentLike']))
			$this->contentLike = $attr['contentLike'];
		if(isset($attr['contentMultiLikeAnd']) && strlen($attr['contentMultiLikeAnd']))
			$this->contentMultiLikeAnd = $attr['contentMultiLikeAnd'];
		if(isset($attr['contentMultiLikeOr']) && strlen($attr['contentMultiLikeOr']))
			$this->contentMultiLikeOr = $attr['contentMultiLikeOr'];
	}
}
