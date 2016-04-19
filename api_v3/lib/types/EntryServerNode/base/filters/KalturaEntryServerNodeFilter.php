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
		if($this->entryIdEqual)
		{
			$entry = entryPeer::retrieveByPK($this->entryIdEqual);
			if(!$entry)
				throw new KalturaAPIException(KalturaErrors::ENTRY_ID_NOT_FOUND, $this->entryIdEqual);
		} 
		else if ($this->entryIdIn)
		{
			$entryIds = explode(',', $this->entryIdIn);
			$entries = entryPeer::retrieveByPKs($entryIds);
			
			$validEntryIds = array();
			foreach ($entries as $entry)
				$validEntryIds[] = $entry->getId();
			
			if (!count($validEntryIds))
			{
				return array(array(), 0);
			}
			
			$entryIds = implode($validEntryIds, ',');
			$this->entryIdIn = $entryIds;
		}

		$c = new Criteria();
		$entryServerNodeFilter = $this->toObject();
		$entryServerNodeFilter->attachToCriteria($c);
		$pager->attachToCriteria($c);

		$dbEntryServerNodes = EntryServerNodePeer::doSelect($c);

		$entryServerNodeList = KalturaEntryServerNodeArray::fromDbArray($dbEntryServerNodes, $responseProfile);
		$response = new KalturaEntryServerNodeListResponse();
		$response->objects = $entryServerNodeList;
		$response->totalCount = count($dbEntryServerNodes);
		return $response;
	}
}
