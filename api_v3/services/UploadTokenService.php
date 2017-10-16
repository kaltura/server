<?php

/**
 *
 * @service uploadToken
 * @package api
 * @subpackage services
 */
class UploadTokenService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		$this->applyPartnerFilterForClass('UploadToken');
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
		$uploadToken->fromObject($uploadTokenDb, $this->getResponseProfile());
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
		$uploadToken->fromObject($uploadTokenDb, $this->getResponseProfile());
		return $uploadToken;
	}
	
	/**
	 * Upload a file using the upload token id, returns an error on failure (an exception will be thrown when using one of the Kaltura clients)
	 * Chunks can be uploaded in parallel and they will be appended according to their resumeAt position.
	 * 
	 * A parallel upload session should have three stages:
	 * 1. A single upload with resume=false and finalChunk=false
	 * 2. Parallel upload requests each with resume=true,finalChunk=false and the expected resumetAt position.
	 *    If a chunk fails to upload it can be re-uploaded.
	 * 3. After all of the chunks have been uploaded a final chunk (can be of zero size) should be uploaded 
	 *    with resume=true, finalChunk=true and the expected resumeAt position. In case an UPLOAD_TOKEN_CANNOT_MATCH_EXPECTED_SIZE exception
	 *    has been returned (indicating not all of the chunks were appended yet) the final request can be retried.     
	 * 
	 * @action upload
	 * @param string $uploadTokenId
	 * @param file $fileData
	 * @param bool $resume
	 * @param bool $finalChunk
	 * @param float $resumeAt
	 * @return KalturaUploadToken
	 */
	function uploadAction($uploadTokenId, $fileData, $resume = false, $finalChunk = true, $resumeAt = -1)
	{
		$this->restrictPeerToCurrentUser();
		$uploadTokenDb = UploadTokenPeer::retrieveByPK($uploadTokenId);
		if (is_null($uploadTokenDb))
			throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_NOT_FOUND);

		// if the token was generated on another datacenter, proxy the upload action there
		$remoteDCHost = kUploadTokenMgr::getRemoteHostForUploadToken($uploadTokenId, kDataCenterMgr::getCurrentDcId());
		if($remoteDCHost)
		{
			kFileUtils::dumpApiRequest($remoteDCHost);
		}

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
				case kUploadTokenException::UPLOAD_TOKEN_CANNOT_MATCH_EXPECTED_SIZE:
					throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_CANNOT_MATCH_EXPECTED_SIZE);
				case kUploadTokenException::UPLOAD_TOKEN_FILE_TYPE_RESTRICTED:
					throw new KalturaAPIException(KalturaErrors::UPLOAD_TOKEN_FILE_TYPE_RESTRICTED_FOR_UPLOAD);
				default:
					throw $ex;
			}
		}
		$uploadToken = new KalturaUploadToken();
		$uploadToken->fromObject($uploadTokenDb, $this->getResponseProfile());
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

                // in case a filename filter was passed enforce a statusIn filter in order to limit slow db queries
                if ($filter->fileNameEqual && $filter->statusEqual == null && $filter->statusIn == null)
                        $filter->statusIn = implode(",", array(KalturaUploadTokenStatus::PENDING, KalturaUploadTokenStatus::PARTIAL_UPLOAD, KalturaUploadTokenStatus::FULL_UPLOAD));
 
		// create the filter
		$uploadTokenFilter = new UploadTokenFilter();
		$filter->toObject($uploadTokenFilter);
		$c = new Criteria();
		$uploadTokenFilter->attachToCriteria($c);
		$totalCount = UploadTokenPeer::doCount($c);
		$pager->attachToCriteria($c);
		
		$list = UploadTokenPeer::doSelect($c);
		
		// create the response object
		$newList = KalturaUploadTokenArray::fromDbArray($list, $this->getResponseProfile());
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
