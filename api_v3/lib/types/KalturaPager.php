<?php
/**
 * The KalturaPager object enables paging management to be applied upon service list/search actions.
 * @package api
 * @subpackage objects
 */
class KalturaPager extends KalturaObject
{
    const MIN_PAGE_INDEX = 1;
    const MAX_PAGE_SIZE = 500;

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

    private static $map_between_objects = array(
        'pageSize',
        'pageIndex',
    );

    /* (non-PHPdoc)
	 * @see KalturaObject::getMapBetweenObjects()
	 */
    public function getMapBetweenObjects()
    {
        return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
    }

    public function toObject($object = null, $skipProperties = array())
    {
        if(!$object)
        {
            $object = new kPager();
        }

        return parent::toObject($object, $skipProperties);
    }

    public function calcPageSize()
    {
        return max(min($this->pageSize, self::MAX_PAGE_SIZE), 0);
    }

    public function calcPageIndex()
    {
        return max(self::MIN_PAGE_INDEX, $this->pageIndex);
    }

    public function calcOffset()
    {
        return ($this->calcPageIndex() - 1) * $this->calcPageSize();
    }

}
