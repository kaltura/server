<?php
/**
 * @package plugins.quiz
 * @subpackage api.filters
 */
class KalturaQuizUserEntryFilter extends KalturaQuizUserEntryBaseFilter
{
	/**
	 * @var int
	 */
	public $versionEqual;

	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$this->typeEqual = QuizPlugin::getApiValue(QuizUserEntryType::QUIZ);
		$response = parent::getListResponse($pager, $responseProfile);
		return $response;
	}

	static private $map_between_objects = array
	(
		"versionEqual" => "_eq_version",
	);

	static private $order_by_map = array
	(
		"+version" => "+version",
		"-version" => "-version",
	);

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	public function getOrderByMap()
	{
		return array_merge(parent::getOrderByMap(), self::$order_by_map);
	}
}
