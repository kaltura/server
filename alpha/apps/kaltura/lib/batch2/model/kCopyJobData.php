<?php
/**
 * @package Core
 * @subpackage model.data
 */
class kCopyJobData extends kJobData
{
	/**
	 * The filter should return the list of objects that need to be copied.
	 * @var baseObjectFilter
	 */
	private $filter;
	
	/**
	 * Indicates the last id that copied, used when the batch crached, to re-run from the last crash point.
	 * @var int
	 */
	private $lastCopyId;
	
	/**
	 * Template object to overwrite attributes on the copied object
	 * @var object
	 */
	private $templateObject;
	
	/**
	 * @return baseObjectFilter $filter
	 */
	public function getFilter()
	{
		return $this->filter;
	}

	/**
	 * @param baseObjectFilter $filter
	 */
	public function setFilter(baseObjectFilter $filter)
	{
		$this->filter = $filter;
	}
	
	/**
	 * @return int $lastCopyId
	 */
	public function getLastCopyId()
	{
		return $this->lastCopyId;
	}

	/**
	 * @param int $lastCopyId
	 */
	public function setLastCopyId($lastCopyId)
	{
		$this->lastCopyId = $lastCopyId;
	}
	
	/**
	 * @return object $templateObject
	 */
	public function getTemplateObject()
	{
		return $this->templateObject;
	}

	/**
	 * @param object $templateObject
	 */
	public function setTemplateObject($templateObject)
	{
		$this->templateObject = $templateObject;
	}
}
