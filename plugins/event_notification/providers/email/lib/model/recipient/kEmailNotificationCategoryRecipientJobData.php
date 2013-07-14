<?php
/**
 * Class representing the finalized implicit categoryId recipient provider passed into the batch mechanism (after application of scope).
 *
 * @package plugins.emailNotification
 * @subpackage model.data
 */
class kEmailNotificationCategoryRecipientJobData extends kEmailNotificationRecipientJobData
{
	/**
	 * @var categoryKuserFilter
	 */
	protected $categoryUserFilter;

	/**
	 * @return categoryKuserFilter
	 */
	public function getCategoryUserFilter() {
		return $this->categoryUserFilter;
	}

	/**
	 * @param categoryKuserFilter $categoryUserFilter
	 */
	public function setCategoryUserFilter(categoryKuserFilter $categoryUserFilter) {
		$this->categoryUserFilter = $categoryUserFilter;
	}


}