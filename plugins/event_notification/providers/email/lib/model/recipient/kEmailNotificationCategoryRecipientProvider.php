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
	 * Additional filter
	 * @var categoryKuserFilter
	 */
	protected $categoryUserFilter;
	
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
		
		if(!$this->categoryId)
			return $ret;
		
		if ($this->categoryId instanceof kStringField)
			$this->categoryId->setScope($scope);
		
		$implicitCategoryId = $this->categoryId->getValue();
		
		$categoryUserFilter = new categoryKuserFilter();
		$categoryUserFilter->set('_matchor_permission_names', PermissionName::CATEGORY_SUBSCRIBE);
		if ($this->categoryUserFilter)
		{
			$categoryUserFilter = $this->categoryUserFilter;
		}
		$categoryUserFilter->setCategoryIdEqual($implicitCategoryId);
		$ret->setCategoryUserFilter($categoryUserFilter);
		
		return $ret;
	}
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