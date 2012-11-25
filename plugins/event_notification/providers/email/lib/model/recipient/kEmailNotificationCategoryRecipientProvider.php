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
	 * @var kStringField
	 */
	protected $categoryId;


	/**
	 * @return kStringField
	 */
	public function getCategoryId() {
		return $this->categoryId;
	}

	/**
	 * @param kStringValue $category_id
	 */
	public function setCategoryId($category_id) {
		$this->categoryId = $category_id;
	}
	
	
	/* (non-PHPdoc)
	 * @see kEmailNotificationRecipientProvider::getScopedProviderJobData()
	 */
	public function getScopedProviderJobData(kScope $scope = null) 
	{
		$ret = new kEmailNotificationCategoryRecipientJobData();
		if ($this->getCategoryId() instanceof kObjectIdField)
		{
			$this->categoryId->setScope($scope);
			$categoryId = $this->categoryId->getValue();
			KalturaLog::info("Implicit categoryId value: $categoryId");
			$ret->setCategoryId($categoryId);
		}
		
		return $ret;
	}


	
	
}