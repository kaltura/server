<?php
/**
 * Core class for a provider for the recipients of category-related notifications.
 *
 * @package plugins.emailNotification
 * @subpackage model.data
 */
class kEmailNotificationCategoryRecipientProvider extends kEmailNotificationRecipientProvider
{
	/**
	 * ID of the category to whose subscribers the email should be sent
	 * @var kStringValue
	 */
	protected $category_id;


	/**
	 * @return the $category_id
	 */
	public function getCategoryId() {
		return $this->category_id;
	}

	/**
	 * @param field_type $category_id
	 */
	public function setCategoryId($category_id) {
		$this->category_id = $category_id;
	}
	
	/* (non-PHPdoc)
	 * @see kEmailNotificationRecipientProvider::applyScope()
	 */
	public function applyScope(kScope $scope) 
	{
		$ret = new kEmailNotificationCategoryRecipientJobData();
		if ($this->getCategoryId() instanceof kObjectIdField)
		{
			$ret->setCategoryId($this->category_id->getValue());
		}
		
		return $ret;
	}


	
	
}