<?php
/**
 * Annotation service - Video Annotation
 *
 * @service annotation
 * @throws KalturaErrors::SERVICE_FORBIDDEN
 */
class AnnotationService extends KalturaBaseService
{	
	public function initService($partnerId, $puserId, $ksStr, $serviceName, $action)
	{
		parent::initService($partnerId, $puserId, $ksStr, $serviceName, $action);
		
		myPartnerUtils::addPartnerToCriteria ( new AnnotationPeer() , $this->getPartnerId() , $this->private_partner_data , $this->partnerGroup() , $this->kalturaNetwork()  );
				
		if(!AnnotationPlugin::isAllowedPartner(kCurrentContext::$master_partner_id))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN);
	}
	
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
		kalturalog::debug("annotation service listAction");
		
		if (!$filter)
			$filter = new KalturaAnnotationFilter();
			
		$c = new Criteria();
		$c->add(AnnotationPeer::STATUS,AnnotationStatus::ANNOTATION_STATUS_READY);
		
		$annotationFilter = new AnnotationFilter();
		$filter->toObject($annotationFilter);
		
		$annotationFilter->attachToCriteria($c);
		$count = AnnotationPeer::doCount($c);
		
		if ($pager)
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
		kalturalog::debug("annotation service addAction");
		$annotation->validatePropertyNotNull("entryId");
		$annotation->validateParentId($annotation);
		$annotation->validateEntryId($annotation);
		$annotation->validateStartTime($annotation);
		$annotation->validateEndTime($annotation);				
		if($annotation->text != null)
			$annotation->validatePropertyMaxLength("text", AnnotationPeer::MAX_ANNOTATION_TEXT);
		if($annotation->tags != null)
			$annotation->validatePropertyMaxLength("tags", AnnotationPeer::MAX_ANNOTATION_TAGS);

		$dbAnnotation = $annotation->toInsertableObject();
		$dbAnnotation->setId($dbAnnotation->getUniqueAnnotationId());
		$dbAnnotation->setPartnerId($this->getPartnerId());
		$dbAnnotation->setStatus(AnnotationStatus::ANNOTATION_STATUS_READY); 
		$dbAnnotation->setKuserId($this->getKuser()->getId()); 
					
		$created = $dbAnnotation->save();
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
	 * @param string $id 
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
	 * delete annotation by id, and delete al childs annotations
	 * 
	 * @action delete
	 * @param string $id 
	 * @throws KalturaErrors::INVALID_OBJECT_ID
	 * @throws AnnotationStatus::ANNOTATION_STATUS_DELETED
	 */		
	function deleteAction($id)
	{
		kalturalog::debug("annotation service deleteAction");
		$dbAnnotation = AnnotationPeer::retrieveByPK( $id );
		if(!$dbAnnotation)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);

		$dbAnnotation->setStatus(AnnotationStatus::ANNOTATION_STATUS_DELETED);
		$dbAnnotation->save();
	}
	
	/**
	 * Update annotation by id 
	 * 
	 * @action update
	 * @param string $id
	 * @param KalturaAnnotation $annotation
	 * @return KalturaAnnotation
	 */
	function updateAction($id, KalturaAnnotation $annotation)
	{
		kalturalog::debug("annotation service updateAction");

		$dbAnnotation = AnnotationPeer::retrieveByPK($id);
		
		if (!$dbAnnotation)
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_ID, $id);
		
		if($annotation->text !== null)
			$annotation->validatePropertyMaxLength("text", AnnotationPeer::MAX_ANNOTATION_TEXT);
		
		if($annotation->tags !== null)
			$annotation->validatePropertyMaxLength("tags", AnnotationPeer::MAX_ANNOTATION_TAGS);
		
		if($annotation->entryId !== null)
			$annotation->validateEntryId($annotation, $id);
		
		if($annotation->parentId !== null)
			$annotation->validateParentId($annotation, $id);
		
		if($annotation->startTime !== null)
			$annotation->validateStartTime($annotation, $id);
		
		if($annotation->endTime !== null)
			$annotation->validateEndTime($annotation, $id);
					
		$dbAnnotation = $annotation->toUpdatableObject($dbAnnotation);
				
		$dbAnnotation->setKuserId($this->getKuser()->getId()); 
		$dbAnnotation->save();
		
		$annotation->fromObject($dbAnnotation);
		return $annotation;
	}
}
