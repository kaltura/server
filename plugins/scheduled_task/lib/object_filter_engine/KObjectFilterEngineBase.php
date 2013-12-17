<?php

/**
 * @package plugins.scheduledTask
 * @subpackage lib.objectFilterEngine
 */
abstract class KObjectFilterEngineBase
{
	/**
	 * @var int
	 */
	private $_pageSize;

	/**
	 * @var int
	 */
	private $_pageIndex;

	public function __construct()
	{
	}

	/**
	 * @param KalturaFilter $objectFilter
	 * @return array
	 */
	abstract function query(KalturaFilter $objectFilter);

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
}