<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectFilterEngine
 */
abstract class KObjectFilterEngineBase
{
	/**
	 * @var KalturaClient
	 */
	protected $_client;

	/**
	 * @var int
	 */
	private $_pageSize;

	/**
	 * @var int
	 */
	private $_pageIndex;

	public function __construct(KalturaClient $client)
	{
		$this->_client = $client;
	}

	/**
	 * @param KalturaFilter $filter
	 * @return KalturaObjectListResponse
	 */
	abstract function query(KalturaFilter $filter);

	/**
	 * @param int $pageIndex
	 */
	public function setPageIndex($pageIndex)
	{
		$this->_pageIndex = $pageIndex;
	}

	/**
	 * @return int
	 */
	public function getPageIndex()
	{
		return $this->_pageIndex;
	}

	/**
	 * @param int $pageSize
	 */
	public function setPageSize($pageSize)
	{
		$this->_pageSize = $pageSize;
	}

	/**
	 * @return int
	 */
	public function getPageSize()
	{
		return $this->_pageSize;
	}

	/**
	 * @return KalturaFilterPager
	 */
	public function getPager()
	{
		$pager = new KalturaFilterPager();
		$pager->pageIndex = $this->_pageIndex;
		$pager->pageSize = $this->_pageSize;
		return $pager;
	}
}