<?php
/**
 * Annotation service
 *
 * @service annotation
 */
class AnnotationService extends KalturaBaseService
{
	
	/**
	 * List annotation objects by filter and pager
	 * 
	 * @action list
	 * @param KalturaAnnotationFilter $filter
	 * @param KalturaFilterPager $pager
	 * @return KalturaAnnotationListResponse
	 */
	function listAction(KalturaAnnotationFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaAnnotationFilter;
			
		$annotationFilter = $filter->toObject();
		
		$c = new Criteria();
		$annotationFilter->attachToCriteria($c);
		$count = AnnotationPeer::doCount($c);
		
		if (!$pager)
			$pager = new KalturaFilterPager();
			
		$pager->attachToCriteria($c);
		$list = AnnotationPeer::doSelect($c);
		
		$response = new KalturaAnnotationListResponse();
		$response->objects = KalturaAnnotationArray::fromDbArray($list);
		$response->totalCount = $count;
		
		return $response;
	}
	
	/**
	 * Allows you to add an annotation object and Annotation content associated with Kaltura object
	 * 
	 * @action add
	 * @param KalturaAnnotation $annotation
	 * @return KalturaAnnotation
	 */
	function addAction(KalturaAnnotation $annotation)
	{
		//TODO
		$annotation->validatePropertyNotNull("entryId");
		$annotation->validatePropertyNotNull("start_time");
		$annotation->validatePropertyMaxLength("data", 1000); //TODO - WHAT IS THE MAX LENGTH
		
		$dbAnnotation = $annotation->toInsertableObject();
		$dbAnnotation->setPartnerId($this->getPartnerId());
		$dbAnnotation->setStatus(Annotation::ANNOTATION_STATUS_READY);
		
		$created = $dbAnnotation->save(); //TODO SAVE()
		if(!$created)
			return null;
		
		$annotation = new KalturaAnnotation();
		$annotation->fromObject($dbAnnotation);
		
		return $annotation;
	}
	
	/**
	 * Retrieve an Annotation object by id
	 * 
	 * @action get
	 * @param int $id 
	 * @return KalturaAnnotation
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	function getAction($id)
	{
		$dbAnnotation = AnnotationPeer::retrieveByPK( $id );
		
		if(!$dbAnnotation)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
			
		$annotation = new KalturaAnnotation();
		$annotation->fromObject($dbAnnotation);
		
		return $annotation;
	}
	
	/**
	 * delete annotation by id
	 * 
	 * @action delete
	 * @param int $id 
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 */		
	function deleteAction($id)
	{
		$dbAnnotation = AnnotationPeer::retrieveByPK( $id );
		
		if(!$dbAnnotation)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
			
		$dbAnnotation->setStatus(Annotation::ANNOTATION_STATUS_DELETED);
		$dbAnnotation->save(); //TODO SAVE()
	}
	
	/**
	 * Update annotation by id 
	 * 
	 * @action update
	 * @param int $annotationId
	 * @param KalturaAnnotation $annotation
	 * @return KalturaAnnotation
	 */
	function updateAction($annotationId, KalturaAnnotation $annotation)
	{
		// TODO: validate object
		$dbAnnotation = SystemUserPeer::retrieveByPK($annotationId);
		if (!$dbAnnotation)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $annotationId);
			
		//TODO - validate logic if needed before update.
			
		$dbAnnotation = $annotation->toUpdatableObject($dbAnnotation);
		$dbAnnotation->save();
		
		$annotation->fromObject($dbAnnotation);
		return $annotation;
	}
}
