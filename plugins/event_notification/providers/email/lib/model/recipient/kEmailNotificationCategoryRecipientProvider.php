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
     * ID of the category to whose subscribers the email should be sent
     * @var kStringValue
     */
    protected $categoryIds;

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

    /**
     * @return kStringValue
     */
    public function getCategoryIds() {
        return $this->categoryIds;
    }

    /**
     * @param kStringValue $category_id
     */
    public function setCategoryIds($category_ids) {
        $this->categoryIds = $category_ids;
    }
	
	
	/* (non-PHPdoc)
	 * @see kEmailNotificationRecipientProvider::getScopedProviderJobData()
	 */
	public function getScopedProviderJobData(kScope $scope = null)
    {
        $ret = new kEmailNotificationCategoryRecipientJobData();

        if (!$this->categoryId && !$this->categoryIds)
        {
            return $ret;
        }

        $implicitCategoryId = null;
        if ($this->categoryId && $this->categoryId instanceof kStringField)
        {
            $this->categoryId->setScope($scope);
            $implicitCategoryId = $this->categoryId->getValue();
        }

        $implicitCategoryIds = null;
        if ($this->categoryIds && $this->categoryIds instanceof kStringField)
        {

            $this->categoryIds->setScope($scope);
            $implicitCategoryIds = $this->categoryIds->getValue();
        }

        if ($implicitCategoryIds && $implicitCategoryId)
        {
            $implicitCategoryIds .= ",$implicitCategoryId";
        }

		$categoryUserFilter = new categoryKuserFilter();
		$categoryUserFilter->set('_matchor_permission_names', PermissionName::CATEGORY_SUBSCRIBE);
		if ($this->categoryUserFilter)
		{
			$categoryUserFilter = $this->categoryUserFilter;
		}

		if ($implicitCategoryIds)
        {
            $categoryUserFilter->set('_in_category_id', $implicitCategoryIds);
        }
        else
        {
            $categoryUserFilter->setCategoryIdEqual($implicitCategoryId);
        }
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