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
	
	public function attachToCriteria ( Criteria $c )
	{
		$this->pageSize = max(min($this->pageSize, baseObjectFilter::getMaxInValues()), 0);
		$c->setLimit( $this->pageSize );
		
		$this->pageIndex = max(self::MIN_PAGE_INDEX, $this->pageIndex);		
		$offset = ($this->pageIndex - 1) * $this->pageSize;
		$c->setOffset( $offset );
	}
	
	public static function detachFromCriteria(Criteria $c)
	{
		$c->setOffset(0);
		$c->setLimit(-1);
	}
}
