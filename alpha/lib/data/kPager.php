<?php
/**
 * @package Core
 * @subpackage model.data
 */

class kPager
{

    /**
     * The number of objects to retrieve. (Default is 30, maximum page size is 500).
     *
     * @var int
     */
    protected $pageSize = 30;

    /**
     * The page number for which {pageSize} of objects should be retrieved (Default is 1).
     *
     * @var int
     */
    protected $pageIndex = 1;

    /**
     * @return the $pageSize
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @return the $pageIndex
     */
    public function getPageIndex()
    {
        return $this->pageIndex;
    }

    /**
     * @param int $pageSize
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    /**
     * @param int $pageIndex
     */
    public function setPageIndex($pageIndex)
    {
        $this->pageIndex = $pageIndex;
    }

    public function calcOffset()
    {
        return ($this->getPageIndex() - 1) * $this->getPageSize();
    }

}
