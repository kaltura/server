<?php
/**
 * @package plugins.bulkUploadFilter
 * @subpackage model.data
 */
class kBulkUploadFilterJobData extends kBulkUploadJobData
{
	/**
	 * Filter for extracting the objects list to upload
	 * @var baseObjectFilter
	 */
	protected $filter;
		
	/**
	 * Template object for new object creation
	 * @var object
	 */
	protected $templateObject;
	
	/**
	 * @return the $filter
	 */
	public function getFilter() {
		return $this->filter;
	}

	/**
	 * @param baseObjectFilter $filter
	 */
	public function setFilter($filter) {
		$this->filter = $filter;
	}
	
	/**
	 * @return the $templateObject
	 */
	public function getTemplateObject() {
		return $this->templateObject;
	}

	/**
	 * @param object $templateObject
	 */
	public function setTemplateObject($templateObject) {
		$this->templateObject = $templateObject;
	}
}
