<?php

/**
 *
 * @service uploadToken
 * @package api
 * @subpackage services
 */
class UploadTokenService extends KalturaBaseService
{
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);
		parent::applyPartnerFilterForClass(new UploadTokenPeer());
	}
	
	/**
	 * Adds new upload token to upload a file
	 * 
	 * @action add
	 * @param KalturaUploadToken $uploadToken
	 * @return KalturaUploadToken
	 */
	function addAction(KalturaUploadToken $uploadToken = null)
	{
		if (is_null($uploadToken))
			$uploadToken = new KalturaUploadToken();
			
		// prepare the db object
		$uploadTokenDb = new UploadToken();
		
		// validate
		$uploadToken->toInsertableObject($uploadTokenDb);
		
		// set additional properties
		$uploadTokenDb->setPartnerId($this->getPartnerId());
		$uploadTokenDb->setKuserId($this->getKuser()->getId());
		
		// use the upload token manager to add the token
		$uploadTokenMgr = new kUploadTokenMgr($uploadTokenDb);
		$uploadTokenMgr->saveAsNewUploadToken();
		$uploadTokenDb = $uploadTokenMgr->getUploadToken();
		
		$uploadToken->fromObject($uploadTokenDb);
		
		return $uploadToken;
	}
	
	/**
	 * Get upload token by id
	 * 
	 * @action get
	 * @param string $uploadTokenId
	 * @return KalturaUploadToken
	 */
	function getAction($uploadTokenId)
	{
		$this->restrictPeerToCurrentUser();
		$uploadTokenDb = UploadTokenPeer::retrieveByPK($uploadTokenId);
		if (is_null($uploadTokenDb))
			throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_NOT_FOUND);
			
		$uploadToken = new KalturaUploadToken();
		$uploadToken->fromObject($uploadTokenDb);
		return $uploadToken;
	}
	
	/**
	 * Upload a file using the upload token id, returns an error on failure (an exception will be thrown when using one of the Kaltura clients) 
	 * 
	 * @action upload
	 * @param string $uploadTokenId
	 * @param file $fileData
	 * @param bool $resume
	 * @param bool $finalChunk
	 * @param int $resumeAt
	 * @return KalturaUploadToken
	 */
	function uploadAction($uploadTokenId, $fileData, $resume = false, $finalChunk = true, $resumeAt = -1)
	{
		$this->restrictPeerToCurrentUser();
		$uploadTokenDb = UploadTokenPeer::retrieveByPK($uploadTokenId);
		if (is_null($uploadTokenDb))
			throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_NOT_FOUND);
			
		$uploadTokenMgr = new kUploadTokenMgr($uploadTokenDb);
		try
		{
			$uploadTokenMgr->uploadFileToToken($fileData, $resume, $finalChunk, $resumeAt);
		}
		catch(kUploadTokenException $ex)
		{
			switch($ex->getCode())
			{
				case kUploadTokenException::UPLOAD_TOKEN_INVALID_STATUS:
					throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_INVALID_STATUS_FOR_UPLOAD);
				case kUploadTokenException::UPLOAD_TOKEN_FILE_NAME_IS_MISSING_FOR_UPLOADED_FILE:
				case kUploadTokenException::UPLOAD_TOKEN_UPLOAD_ERROR_OCCURRED:
				case kUploadTokenException::UPLOAD_TOKEN_FILE_IS_NOT_VALID:
				 	throw new KalturaAPIException(KalturaErrors::UPLOAD_ERROR);
				case kUploadTokenException::UPLOAD_TOKEN_FILE_NOT_FOUND_FOR_RESUME:
					throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_CANNOT_RESUME);
				case kUploadTokenException::UPLOAD_TOKEN_RESUMING_NOT_ALLOWED:
					throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_RESUMING_NOT_ALLOWED);
				case kUploadTokenException::UPLOAD_TOKEN_RESUMING_INVALID_POSITION:
					throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_RESUMING_INVALID_POSITION);
				default:
					throw $ex;
			}
		}
		$uploadToken = new KalturaUploadToken();
		$uploadToken->fromObject($uploadTokenDb);
		return $uploadToken;
	}

	/**
	 * Deletes the upload token by upload token id
	 * 
	 * @action delete
	 * @param string $uploadTokenId
	 */
	function deleteAction($uploadTokenId)
	{
		$this->restrictPeerToCurrentUser();
		$uploadTokenDb = UploadTokenPeer::retrieveByPK($uploadTokenId);
		if (is_null($uploadTokenDb))
			throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_NOT_FOUND);
			
		$uploadTokenMgr = new kUploadTokenMgr($uploadTokenDb);
		try
		{
			$uploadTokenMgr->deleteUploadToken();
		}
		catch(kCoreException $ex)
		{
			throw $ex;
		}
	}
	
	/**
	 * List upload token by filter with pager support. 
	 * When using a user session the service will be restricted to users objects only.
	 * 
	 * @action list
	 * @param KalturaUploadTokenFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaUploadTokenListResponse
	 */
	function listAction(KalturaUploadTokenFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaUploadTokenFilter();
			
		if (!$pager)
			$pager = new KalturaFilterPager();
		
		$this->restrictPeerToCurrentUser();
			
		// translate the user id (puser id) to kuser id
		if ($filter->userIdEqual !== null)
		{
			$kuser = kuserPeer::getKuserByPartnerAndUid($this->getPartnerId(), $filter->userIdEqual);
			if ($kuser)
				$filter->userIdEqual = $kuser->getId();
			else 
				$filter->userIdEqual = -1; // no result will be returned when the user is missing
		}
		
		// create the filter
		$uploadTokenFilter = new UploadTokenFilter();
		$filter->toObject($uploadTokenFilter);
		$c = new Criteria();
		$uploadTokenFilter->attachToCriteria($c);
		$totalCount = UploadTokenPeer::doCount($c);
		$pager->attachToCriteria($c);
		
		$list = UploadTokenPeer::doSelect($c);
		
		// create the response object
		$newList = KalturaUploadTokenArray::fromUploadTokenArray($list);
		$response = new KalturaUploadTokenListResponse();
		$response->objects = $newList;
		$response->totalCount = $totalCount;
		return $response;
	}
	
	/**
	 * When using user session, restrict the peer to users tokens only
	 */
	protected function restrictPeerToCurrentUser()
	{
		if (!$this->getKs() || !$this->getKs()->isAdmin())
		{
			UploadTokenPeer::getCriteriaFilter()->getFilter()->addAnd(UploadTokenPeer::KUSER_ID, $this->getKuser()->getId());
		}
	}
}