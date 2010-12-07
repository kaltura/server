<?php
/**
 * AnnotationSession service
 *
 * @service annotationSession
 */
class AnnotationSessionService extends KalturaBaseService
{
	
	/**
	 * List annotationSession objects by filter and pager
	 * 
	 * @action list
	 * @param KalturaAnnotationSessionFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaAnnotationSessionListResponse
	 */
	function listAction(KalturaAnnotationSessionFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaAnnotationSessionFilter;
			
		$annotationSessionFilter = $filter->toObject();
		
		$c = new Criteria();
		$annotationSessionFilter->attachToCriteria($c);
		$count = AnnotationSessionPeer::doCount($c);
		
		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$pager->attachToCriteria($c);
		$list = AnnotationSessionPeer::doSelect($c);
		
		$response = new KalturaAnnotationSessionListResponse();
		$response->objects = KalturaAnnotationSessionArray::fromDbArray($list);
		$response->totalCount = $count;
		
		return $response;
	}
	
	/**
	 * Allows you to add an annotationSession object and AnnotationSession content associated with Kaltura object
	 * 
	 * @action add
	 * @param KalturaAnnotationSession $annotationSession
	 * @param int $publish
	 * @return KalturaAnnotationSession
	 */
	function addAction(KalturaAnnotationSession $annotationSession, $publish = 1)
	{
		//TODO - add types
		$annotationSession->validatePropertyNotNull("entryId");
		
		$dbAnnotationSession = $annotationSession->toInsertableObject();
		$dbAnnotationSession->setPartnerId($this->getPartnerId());
		
		if($publish){
			$dbAnnotationSession->setStatus(AnnotationSession::ANNOTATION_SESSION_STATUS_READY);
		}else{
			$dbAnnotationSession->setStatus(AnnotationSession::ANNOTATION_SESSION_STATUS_DRAFT);
		}
		
		$created = $dbAnnotationSession->save(); //TODO SAVE()
		if(!$created)
			return null;
		
		$annotationSession = new KalturaAnnotationSession();
		$annotationSession->fromObject($dbAnnotationSession);
		
		return $annotationSession;
	}
	
	/**
	 * Retrieve an AnnotationSession object by id
	 * 
	 * @action get
	 * @param int $id 
	 * @return KalturaAnnotationSession
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	function getAction($id)
	{
		$dbAnnotationSession = AnnotationSessionPeer::retrieveByPK( $id );
		
		if(!$dbAnnotationSession)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
			
		$annotationSession = new KalturaAnnotationSession();
		$annotationSession->fromObject($dbAnnotationSession);
		
		return $annotationSession;
	}
	
	/**
	 * delete annotationSession by id
	 * 
	 * @action delete
	 * @param int $id
	 * @param int $deleteChildsAnnotations  
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	function deleteAction($id, $deleteChildsAnnotations)
	{
		$dbAnnotationSession = AnnotationSessionPeer::retrieveByPK( $id );
		
		if(!$dbAnnotationSession)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
			
		if($deleteChildsAnnotations)
		{
			//TODO
		}
			
		$dbAnnotationSession->setStatus(AnnotationSession::ANNOTATION_SESSION_STATUS_DELETED);
		$dbAnnotationSession->save(); //TODO SAVE()
	}
	
	/**
	 * appendAnnotation to annotationSession by id of annotation and id of annotationSession
	 * 
	 * @action appendAnnotation
	 * @param int $annotationSessionId
	 * @param int $annotationIds
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */
	function appendAnnotation($annotationSessionId, $annotationIds)
	{
		$dbAnnotationSession = AnnotationSessionPeer::retrieveByPK( $annotationSessionId );
		
		if(!$dbAnnotationSession)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $annotationSessionId);
			
		$annotationIdsArr = explode(",",$annotationIds);
		
		foreach($annotationIdsArr as $annotationId)
		{
			//TODO - update annotation to be assigned to session;
		}
		
		$dbAnnotationSession->save(); //to set updated_at
		
		$annotationSession = new KalturaAnnotationSession();
		$annotationSession->fromObject($dbAnnotationSession);
		
		return $annotationSession;		
	}
	
	/**
	 * publish annotationSession by id
	 * 
	 * @action get
	 * @param int $id 
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	function publishAction($id)
	{
		$dbAnnotationSession = AnnotationSessionPeer::retrieveByPK( $id );
		
		if(!$dbAnnotationSession)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
			
		$dbAnnotationSession->setStatus(AnnotationSession::ANNOTATION_SESSION_STATUS_READY);
		$dbAnnotationSession->save(); //TODO SAVE()
	}
	
	/**
	 * Update annotationSession by id 
	 * 
	 * @action update
	 * @param int $annotationSessionId
	 * @param KalturaAnnotationSession $annotationSession
	 * @return KalturaAnnotationSession
	 */
	function updateAction($annotationSessionId, KalturaAnnotationSession $annotationSession)
	{
		// TODO: validate object
		$dbAnnotationSession = SystemUserPeer::retrieveByPK($annotationSessionId);
		if (!$dbAnnotationSession)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $annotationSessionId);
			
		//TODO - validate logic if needed before update.
			
		$dbAnnotationSession = $annotationSession->toUpdatableObject($dbAnnotationSession);
		$dbAnnotationSession->save();
		
		$annotationSession->fromObject($dbAnnotationSession);
		return $annotationSession;
	}
}
