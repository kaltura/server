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
		
		if(!AnnotationPlugin::isAllowedPartner($this->getPartnerId()))
			throw new KalturaAPIException(KalturaErrors::SERVICE_FORBIDDEN, $this->serviceName.'->'.$this->actionName);
	}
}
