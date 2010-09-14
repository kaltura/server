<?php 
/**
 * Implement to add filtering support for API objects
 *
 */
interface IFilterable
{
	/**
	 * Should return the extra filters that are using more than one field
	 * On inherited classes, do not merge the array with the parent class
	 * 
	 * @return array
	 */
	function getExtraFilters(); 
	
	/**
	 * Should return the filter documentation texts
	 *
	 */
	function getFilterDocs();
}