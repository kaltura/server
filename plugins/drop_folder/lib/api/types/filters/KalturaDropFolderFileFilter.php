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
		$count = DropFolderFilePeer::doCount($c);
		
		if($count == 0)
		{
			return $this->getEmptyListResponse();
		}
		
		$pager->attachToCriteria($c);
		$list = DropFolderFilePeer::doSelect($c);
		
		$response = new KalturaDropFolderFileListResponse();
		$response->objects = KalturaDropFolderFileArray::fromDbArray($list, $responseProfile);
		$response->totalCount = $count;
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
