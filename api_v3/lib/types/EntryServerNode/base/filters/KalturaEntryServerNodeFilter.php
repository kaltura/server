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
		if ($this->entryIdEqual == null && $this->entryIdNotIn == null && $this->entryIdIn == null &&
			$this->idEqual == null && $this->idNotIn == null && $this->idIn == null &&
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

		if($this->entryIdIn != null)
		{
			$entryArray = entryPeer::retrieveByPKs($this->entryIdIn);
			if(!$entryArray)
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->entryIdIn);
		}

		$entryServerNodeFilter = $this->toObject();

		$c = KalturaCriteria::create(EntryServerNodePeer::OM_CLASS);
		$entryServerNodeFilter->attachToCriteria($c);
		$dbEntryServerNodes = EntryServerNodePeer::doSelect($c);

		$resultCount = count($dbEntryServerNodes);
		if ($resultCount && $resultCount < $pager->pageSize)
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = categoryEntryPeer::doCount($c);
		}

		$entryServerNodeList = KalturaEntryServerNodeArray::fromDbArray($dbEntryServerNodes, $responseProfile);
		$response = new KalturaEntryServerNodeListResponse();
		$response->objects = $entryServerNodeList;
		$response->totalCount = $totalCount;
		return $response;
	}
}
