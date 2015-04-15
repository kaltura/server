<?php
/**
 * The KalturaFilterPager object enables paging management to be applied upon service list actions. 
 * 
 * @package api
 * @subpackage filters
 */
class KalturaFilterPager extends KalturaObject
{
	const MIN_PAGE_INDEX = 1;
	
	/**
	 * The number of objects to retrieve. (Default is 30, maximum page size is 500).
	 * 
	 * @var int 
	 */
	public $pageSize = 30;
	
	/**
	 * The page number for which {pageSize} of objects should be retrieved (Default is 1).
	 * 
	 * @var int
	 */
	public $pageIndex = 1;	
	
	public function toObject($object = null, $skipProperties = array())
	{
		if(!$object)
		{
			$object = new kFilterPager();
		}
		
		return parent::toObject($object, $skipProperties);
	}

	public function getPageSize()
	{
		return max(min($this->pageSize, baseObjectFilter::getMaxInValues()), 0);
	}

	public function getPageIndex()
	{
		return max(self::MIN_PAGE_INDEX, $this->pageIndex);
	}

	public function getOffset()
	{
		return ($this->getPageIndex() - 1) * $this->getPageSize();
	}
	
	public function attachToCriteria ( Criteria $c )
	{
		$c->setLimit( $this->getPageSize() );
		$c->setOffset( $this->getOffset() );
	}
	
	public static function detachFromCriteria(Criteria $c)
	{
		$c->setOffset(0);
		$c->setLimit(-1);
	}
}
