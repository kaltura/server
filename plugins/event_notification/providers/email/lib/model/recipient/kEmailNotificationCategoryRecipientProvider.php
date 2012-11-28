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
	protected $categoryId;


	/**
	 * @return kStringValue
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
		
		$categoryIdFieldType = get_class($this->getCategoryId());
		KalturaLog::debug("Converting categoryId value for categoryId of type [$categoryIdFieldType]");
		switch ($categoryIdFieldType)
		{
			case 'kObjectIdField':
			case 'kStringField':
				$this->categoryId->setScope($scope);
				$categoryId = $this->categoryId->getValue();
				KalturaLog::info("Implicit categoryId value: $categoryId");
				$ret->setCategoryId($categoryId);
				break;
			case 'kStringValue':
				$categoryId = $this->categoryId->getValue();
				KalturaLog::info("Implicit categoryId value: $categoryId");
				$ret->setCategoryId($categoryId);
				break;
			default:
				$this->categoryId = KalturaPluginManager::loadObject('kStringValue', $categoryIdFieldType);
				break;
		}
		return $ret;
	}


	
	
}