<?php
/**
 * @package plugins.dropFolder
 * @subpackage api.filters
 */
class KalturaDropFolderFileFilter extends KalturaDropFolderFileBaseFilter
{
	/* (non-PHPdoc)
	 * @see KalturaFilter::getCoreFilter()
	 */
	protected function getCoreFilter()
	{
		return new DropFolderFileFilter();
	}
	
	/**
	 * @param KalturaFilterPager $pager
	 * @param KalturaDetachedResponseProfile $responseProfile
	 * @return KalturaListResponse
	 */
	public function getListResponse(KalturaFilterPager $pager, KalturaDetachedResponseProfile $responseProfile = null)
	{
		$dropFolderFileFilter = $this->toObject();
		
		$c = new Criteria();
		$dropFolderFileFilter->attachToCriteria($c);
		$pager->attachToCriteria($c);
		$list = DropFolderFilePeer::doSelect($c);
		
		$resultCount = count($list);
		if ($resultCount && $resultCount < $pager->pageSize)
		{
			$totalCount = ($pager->pageIndex - 1) * $pager->pageSize + $resultCount;
		}
		elseif($resultCount == 0)
		{
			return $this->getEmptyListResponse();
		}
		else
		{
			KalturaFilterPager::detachFromCriteria($c);
			$totalCount = DropFolderFilePeer::doCount($c);
		}
		
		$response = new KalturaDropFolderFileListResponse();
		$response->objects = KalturaDropFolderFileArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $totalCount;
		return $response;
	}
	
	public function getEmptyListResponse()
	{
		$response = new KalturaDropFolderFileListResponse();
		$response->objects = array();
		$response->totalCount = 0;
		return $response;
	}
}
