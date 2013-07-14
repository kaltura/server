<?php
/**
 * Core class for recipient provider which provides a dynamic list of user recipients based on filter.
 *
 * @package plugins.emailNotification
 * @subpackage model.data
 */
class kEmailNotificationUserRecipientProvider extends kEmailNotificationRecipientProvider
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
	
	/* (non-PHPdoc)
	 * @see kEmailNotificationRecipientProvider::getScopedProviderJobData()
	 */
	public function getScopedProviderJobData(kScope $scope = null) {
		$ret = new kEmailNotificationUserRecipientJobData();

		$ret->setFilter($this->filter);
		return $ret;
	}
	


	
}