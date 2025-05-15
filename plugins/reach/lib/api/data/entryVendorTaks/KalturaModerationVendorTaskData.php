<?php

/**
 * @package plugins.reach
 * @subpackage api.objects
 * @relatedService EntryVendorTaskService
 */
class KalturaModerationVendorTaskData extends KalturaVendorTaskData
{
	/**
	 * A comma seperated string of rule IDs.
	 *
	 * @var string
	 */
	public $ruleIds;

	/**
	 * A comma seperated string of policy IDs.
	 *
	 * @var string
	 */
	public $policyIds;

	/**
	 * A comma seperated string of category IDs.
	 *
	 * @var string
	 */
	public $categoryIds;

	/**
	 * JSON string containing the moderation output.
	 *
	 * @var string
	 */
	public $moderationOutputJson;


	private static $map_between_objects = array
	(
		'ruleIds',
		'policyIds',
		'categoryIds',
		'moderationOutputJson'
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function toObject($dbObject = null, $propsToSkip = array())
	{
		if (!$dbObject)
		{
			$dbObject = new kModerationVendorTaskData();
		}

		return parent::toObject($dbObject, $propsToSkip);
	}

	public function validateForInsert($propertiesToSkip = array())
	{
		if($this->categoryIds)
		{
			$this->validateCategories();
		}
		return parent::validateForInsert($propertiesToSkip);
	}

	protected function validateCategories()
	{
		$categoryIds = explode(',', $this->categoryIds);

		foreach ($categoryIds as $categoryId) {
			$category = CategoryPeer::retrieveByPK($categoryId);
			if (!$category) {
				throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_FOUND, $categoryId);
			}
			if ($category->getStatus() != CategoryStatus::ACTIVE) {
				throw new KalturaAPIException(KalturaErrors::CATEGORY_NOT_ACTIVE, $categoryId);
			}
		}
	}
}
