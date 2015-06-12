<?php
/**
 * @package plugins.quiz
 * @subpackage api.filters
 */

class KalturaQuizFilter extends KalturaRelatedFilter {

	static private $map_between_objects = array
	(
		"entryIdEqual" => "_eq_id",
		"entryIdIn" => "_in_id",
	);

	/**
	 * This filter should be in use for retrieving only a specific quiz entry (identified by its entryId).
	 *
	 * @var string
	 */
	public $entryIdEqual;

	/**
	 * This filter should be in use for retrieving few specific quiz entries (string should include comma separated list of entryId strings).
	 *
	 * @var string
	 */
	public $entryIdIn;

	public function getMapBetweenObjects()
	{
		return array_merge(parent::getMapBetweenObjects(), self::$map_between_objects);
	}

	/* (non-PHPdoc)
	 * @see KalturaRelatedFilter::getListResponse()
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null) {
		$entryFilter = new QuizEntryFilter();
		$entryFilter->setPartnerSearchScope(baseObjectFilter::MATCH_KALTURA_NETWORK_AND_PRIVATE);
		$this->toObject($entryFilter);

		$c = KalturaCriteria::create(entryPeer::OM_CLASS);
		if($pager)
			$pager->attachToCriteria($c);

		$entryFilter->attachToCriteria($c);
		$list = entryPeer::doSelect($c);

		$response = new KalturaQuizListResponse();
		$response->objects = KalturaQuizArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $c->getRecordsCount();

		return $response;
	}

	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter() {
		return new QuizEntryFilter();
	}

}