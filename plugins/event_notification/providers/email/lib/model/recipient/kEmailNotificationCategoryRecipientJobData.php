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
	 * CategoryId
	 * @var int
	 */
	protected $categoryId;
	
	/**
	 * @return the $categoryId
	 */
	public function getCategoryId() {
		return $this->categoryId;
	}

	/**
	 * @param field_type $categoryId
	 */
	public function setCategoryId($categoryId) {
		$this->categoryId = $categoryId;
	}

}