<?php
/**
 * Business-process case service lets you get information about processes
 * @service businessProcessCase
 * @package plugins.businessProcessNotification
 * @subpackage api.services
 */
class BusinessProcessCaseService extends KalturaBaseService
{
	public function initService($serviceId, $serviceName, $actionName)
	{
		parent::initService($serviceId, $serviceName, $actionName);
		
		$partnerId = $this->getPartnerId();
		if (!EventNotificationPlugin::isAllowedPartner($partnerId))
			throw new KalturaAPIException(KalturaErrors::FEATURE_FORBIDDEN, EventNotificationPlugin::PLUGIN_NAME);
			
		$this->applyPartnerFilterForClass('EventNotificationTemplate');
	}
	
	/**
	 * Abort business-process case
	 * 
	 * @action abort
	 * @param KalturaEventNotificationEventObjectType $objectType
	 * @param string $objectId
	 * @param int $businessProcessStartNotificationTemplateId
	 *
	 * @throws KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND
	 * @throws KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_CASE_NOT_FOUND
	 * @throws KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_SERVER_NOT_FOUND
	 */		
	public function abortAction($objectType, $objectId, $businessProcessStartNotificationTemplateId)
	{
		$dbObject = kEventNotificationFlowManager::getObject($objectType, $objectId);
		if(!$dbObject)
		{
			throw new KalturaAPIException(KalturaErrors::OBJECT_NOT_FOUND);
		}
		
		$dbTemplate = EventNotificationTemplatePeer::retrieveByPK($businessProcessStartNotificationTemplateId);
		if(!$dbTemplate || !($dbTemplate instanceof BusinessProcessStartNotificationTemplate))
		{
			throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND, $businessProcessStartNotificationTemplateId);
		}
		
		$caseIds = $dbTemplate->getCaseIds($dbObject, false);
		if(!count($caseIds))
		{
			throw new KalturaAPIException(KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_CASE_NOT_FOUND);
		}
		
		$dbBusinessProcessServer = BusinessProcessServerPeer::retrieveByPK($dbTemplate->getServerId());
		if (!$dbBusinessProcessServer)
		{
			throw new KalturaAPIException(KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_SERVER_NOT_FOUND, $dbTemplate->getServerId());
		}
		
		$server = new KalturaActivitiBusinessProcessServer();
		$server->fromObject($dbBusinessProcessServer);
		$provider = kBusinessProcessProvider::get($server);
		
		foreach($caseIds as $caseId)
		{
			$provider->abortCase($caseId);
		}
	}

	/**
	 * Server business-process case diagram
	 * 
	 * @action serveDiagram
	 * @param KalturaEventNotificationEventObjectType $objectType
	 * @param string $objectId
	 * @param int $businessProcessStartNotificationTemplateId
	 * @return file
	 *
	 * @throws KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND
	 * @throws KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_CASE_NOT_FOUND
	 * @throws KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_SERVER_NOT_FOUND
	 */		
	public function serveDiagramAction($objectType, $objectId, $businessProcessStartNotificationTemplateId)
	{
		$dbObject = kEventNotificationFlowManager::getObject($objectType, $objectId);
		if(!$dbObject)
		{
			throw new KalturaAPIException(KalturaErrors::OBJECT_NOT_FOUND);
		}
		
		$dbTemplate = EventNotificationTemplatePeer::retrieveByPK($businessProcessStartNotificationTemplateId);
		if(!$dbTemplate || !($dbTemplate instanceof BusinessProcessStartNotificationTemplate))
		{
			throw new KalturaAPIException(KalturaEventNotificationErrors::EVENT_NOTIFICATION_TEMPLATE_NOT_FOUND, $businessProcessStartNotificationTemplateId);
		}
		
		$caseIds = $dbTemplate->getCaseIds($dbObject, false);
		if(!count($caseIds))
		{
			throw new KalturaAPIException(KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_CASE_NOT_FOUND);
		}
		
		$dbBusinessProcessServer = BusinessProcessServerPeer::retrieveByPK($dbTemplate->getServerId());
		if (!$dbBusinessProcessServer)
		{
			throw new KalturaAPIException(KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_SERVER_NOT_FOUND, $dbTemplate->getServerId());
		}
		
		$businessProcessServer = KalturaBusinessProcessServer::getInstanceByType($dbBusinessProcessServer->getType());
		$businessProcessServer->fromObject($dbBusinessProcessServer);
		$provider = kBusinessProcessProvider::get($businessProcessServer);
		
		$caseId = end($caseIds);
		
		$filename = myContentStorage::getFSCacheRootPath() . 'bpm_diagram/bpm_';
		$filename .= $objectId . '_';
		$filename .= $businessProcessStartNotificationTemplateId . '_';
		$filename .= $caseId . '.jpg';
		
		$provider->getCaseDiagram($caseId, $filename);
		$mimeType = kFile::mimeType($filename);			
		return $this->dumpFile($filename, $mimeType);
	}
	
	/**
	 * list business-process cases
	 * 
	 * @action list
	 * @param KalturaEventNotificationEventObjectType $objectType
	 * @param string $objectId
	 * @return KalturaBusinessProcessCaseArray
	 * 
	 * @throws KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_CASE_NOT_FOUND
	 * @throws KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_SERVER_NOT_FOUND
	 */
	public function listAction($objectType, $objectId)
	{
		$dbObject = kEventNotificationFlowManager::getObject($objectType, $objectId);
		if(!$dbObject)
		{
			throw new KalturaAPIException(KalturaErrors::OBJECT_NOT_FOUND);
		}
		
		$cases = BusinessProcessCasePeer::retrieveCasesByObjectIdObjecType($objectId, $objectType);
		if(!count($cases))
		{
			throw new KalturaAPIException(KalturaBusinessProcessNotificationErrors::BUSINESS_PROCESS_CASE_NOT_FOUND);
		}
		
		$array = new KalturaBusinessProcessCaseArray();
		foreach($cases as $case)
		{
			/* @var $case BusinessProcessCase */
			$dbBusinessProcessServer = BusinessProcessServerPeer::retrieveByPK($case->getServerId());
			if (!$dbBusinessProcessServer)
			{
				KalturaLog::info("Business-Process server [" . $case->getServerId() . "] not found");
				continue;
			}
			
			$businessProcessServer = KalturaBusinessProcessServer::getInstanceByType($dbBusinessProcessServer->getType());
			$businessProcessServer->fromObject($dbBusinessProcessServer);
			$provider = kBusinessProcessProvider::get($businessProcessServer);
			if(!$provider)
			{
				KalturaLog::info("Provider [" . $businessProcessServer->type . "] not found");
				continue;
			}

			$latestCaseId = $case->getCaseId();
			if($latestCaseId)
			{
				try {
					$case = $provider->getCase($latestCaseId);
					$businessProcessCase = new KalturaBusinessProcessCase();
					$businessProcessCase->businessProcessStartNotificationTemplateId = $templateId;
					$businessProcessCase->fromObject($case);
					$array[] = $businessProcessCase;
				} catch (ActivitiClientException $e) {
					KalturaLog::err("Case [$latestCaseId] not found: " . $e->getMessage());
				}
			}
		}
		
		return $array;
	}
}
