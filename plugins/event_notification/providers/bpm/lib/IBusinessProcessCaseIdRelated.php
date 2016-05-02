<?php
/**
 * @package plugins.businessProcessNotification
 * @subpackage lib
 */
interface IBusinessProcessCaseIdRelated
{
	/**
	 * Return the case id from the data object
	 * @return int
	 */
	public function getCaseId();
}