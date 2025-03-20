<?php
/**
 * @package plugins.quiz
 * @subpackage api.filters
 */
class KalturaQuizUserEntryFilter extends KalturaQuizUserEntryBaseFilter
{
	/**
	 * @var KalturaUserEntryExtendedStatus
	 */
	public $extendedStatusEqual;

	/**
	 * @dynamicType KalturaUserEntryExtendedStatus
	 * @var string
	 */
	public $extendedStatusIn;

	/**
	 * @dynamicType KalturaUserEntryExtendedStatus
	 * @var string
	 */
	public $extendedStatusNotIn;

	static private $map_between_objects = array
	(
		"extendedStatusEqual" => "_eq_extended_status",
		"extendedStatusIn" => "_in_extended_status",
		"extendedStatusNotIn" => "_notin_extended_status",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$this->typeEqual = QuizPlugin::getApiValue(QuizUserEntryType::QUIZ);
		UserEntryPeer::setDefaultCriteriaOrderBy(UserEntryPeer::ID);
		$response = parent::getListResponse($pager, $responseProfile);
		return $response;
	}
}
