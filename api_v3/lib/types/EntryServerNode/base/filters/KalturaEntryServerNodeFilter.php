<?php
/**
 * @package api
 * @subpackage filters
 */
class KalturaEntryServerNodeFilter extends KalturaEntryServerNodeBaseFilter
{
	/**
	 * @return baseObjectFilter
	 */
	protected function getCoreFilter()
	{
		return new EntryServerNodeFilter();
	}

	/**
	 * @param KalturaFilterPager $pager
	 * @param KalturaDetachedResponseProfile $responseProfile
	 * @return KalturaListResponse
	 * @throws KalturaAPIException
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		if ($this->entryIdEqual == null && $this->entryIdIn == null &&
			$this->serverTypeEqual == null )
		{
			throw new KalturaAPIException(KalturaErrors::MUST_FILTER_ON_ENTRY_AND_SERVER_TYPE);
		}

		if($this->entryIdEqual != null)
		{
			$entry = entryPeer::retrieveByPK($this->entryIdEqual);
			if(!$entry)
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->entryIdEqual);
		}

		$entryServerNodeFilter = $this->toObject();

		$c = KalturaCriteria::create(EntryServerNodePeer::OM_CLASS);
		$entryServerNodeFilter->attachToCriteria($c);
		$dbEntryServerNodes = EntryServerNodePeer::doSelect($c);

		$pager->attachToCriteria($c);

		$entryServerNodeList = KalturaEntryServerNodeArray::fromDbArray($dbEntryServerNodes, $responseProfile);
		$response = new KalturaEntryServerNodeListResponse();
		$response->objects = $entryServerNodeList;
		$response->totalCount = $c->getRecordsCount();
		return $response;
	}
}
