<?php
/**
 * @package plugins.elasticSearch
 * @subpackage model.items
 */
interface IESearchCategoryEntryItem
{
	/**
	 * define the how to transform the data in the search item: field name ,searchTerm
	 */
	public function transformData();

}
