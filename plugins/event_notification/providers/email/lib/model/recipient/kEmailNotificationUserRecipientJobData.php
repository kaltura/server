<?php
/**
 * Core class representing the finalized implicit recipient list provider to be passed into the batch mechanism
 * 
 * @package plugins.emailNotification
 * @subpackage model.data 
 */
class kEmailNotificationUserRecipientJobData extends kEmailNotificationRecipientJobData
{
	/**
	 * @var kuserFilter
	 */
	protected $filter;
	
	/**
	 * @return kuserFilter $filter
	 */
	public function getFilter() {
		return $this->filter;
	}

	/**
	 * @param kuserFilter $filter
	 */
	public function setFilter(kuserFilter $filter) {
		$this->filter = $filter;
	}

}