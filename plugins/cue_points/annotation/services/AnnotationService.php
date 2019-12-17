<?php
/**
 * Annotation service - Video Annotation
 *
 * @service annotation
 * @package plugins.annotation
 * @subpackage api.services
 * @throws KalturaErrors::SERVICE_FORBIDDEN
 * @deprecated use cuePoint service instead
 */
class AnnotationService extends CuePointService
{
	/**
	 * @return CuePointType or null to limit the service type
	 */
	protected function getCuePointType()
	{
		return AnnotationPlugin::getCuePointTypeCoreValue(AnnotationCuePointType::ANNOTATION);
	}

	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);

	}

	/**
	 * Allows you to add an annotation object associated with an entry
	 *
	 * @action add
	 * @param KalturaAnnotation $annotation
	 * @return KalturaAnnotation
	 */
	function addAction(KalturaCuePoint $annotation)
	{
		return parent::addAction($annotation);
	}

	/**
	 * Clone cuePoint with id to given entry
	 *
	 * @action clone
	 * @param string $id
	 * @param string $entryId
	 * @param string $parentId
	 * @return KalturaAnnotation
	 * @throws KalturaCuePointErrors::INVALID_CUE_POINT_ID
	 * @throws KalturaErrors::ENTRY_ID_NOT_FOUND
	 */
	function cloneAction($id, $entryId, $parentId = null)
	{
		$dbAnnotation = parent::doClone($id, $entryId);
		if ( !$dbAnnotation instanceof annotation)
		{
			throw new KalturaAPIException(KalturaErrors::INVALID_OBJECT_TYPE, get_class($dbAnnotation));
		}
		if ($parentId)
		{
			$dbAnnotation->setParentId($parentId);
		}
		$dbAnnotation->save();
		return KalturaAnnotation::getInstance($dbAnnotation, $this->getResponseProfile());
	}

	/**
	 * Update annotation by id
	 *
	 * @action update
	 * @param string $id
	 * @param KalturaAnnotation $annotation
	 * @return KalturaAnnotation
	 * @throws KalturaCuePointErrors::INVALID_CUE_POINT_ID
	 */
	function updateAction($id, KalturaCuePoint $annotation)
	{
		return parent::updateAction($id, $annotation);
	}
	
	/**
	* List annotation objects by filter and pager
	*
	* @action list
	* @param KalturaAnnotationFilter $filter
	* @param KalturaFilterPager $pager
	* @return KalturaAnnotationListResponse
	*/
	function listAction(KalturaCuePointFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if(!$filter)
			$filter = new KalturaAnnotationFilter();
		
		$filter->cuePointTypeEqual = AnnotationPlugin::getApiValue(AnnotationCuePointType::ANNOTATION);
		
		$list = parent::listAction($filter, $pager);
		$ret = new KalturaAnnotationListResponse();
		$ret->objects = $list->objects;
		$ret->totalCount = $list->totalCount;
		
		return $ret;
	}
}
