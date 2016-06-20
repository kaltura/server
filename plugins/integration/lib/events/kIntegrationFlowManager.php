<?php
/**
 * @package plugins.integration
 * @subpackage lib.events
 */
class kIntegrationFlowManager implements kBatchJobStatusEventConsumer
{
	const EXTERNAL_INTEGRATION_SERVICES_ROLE_NAME = "EXTERNAL_INTEGRATION_SERVICES_ROLE";
	const THREE_DAYS_IN_SECONDS = 259200;

	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::updatedJob()
	 */
	public function updatedJob(BatchJob $dbBatchJob)
	{
		kEventsManager::raiseEvent(new kIntegrationJobClosedEvent($dbBatchJob));
		
		return true;
	}

	/* (non-PHPdoc)
	 * @see kBatchJobStatusEventConsumer::shouldConsumeJobStatusEvent()
	 */
	public function shouldConsumeJobStatusEvent(BatchJob $dbBatchJob)
	{
		if($dbBatchJob->getJobType() != IntegrationPlugin::getBatchJobTypeCoreValue(IntegrationBatchJobType::INTEGRATION))
		{
			return false;
		} 
		 
		$closedStatusList = array(
			BatchJob::BATCHJOB_STATUS_FINISHED,
			BatchJob::BATCHJOB_STATUS_FAILED,
			BatchJob::BATCHJOB_STATUS_ABORTED,
			BatchJob::BATCHJOB_STATUS_FATAL,
			BatchJob::BATCHJOB_STATUS_FINISHED_PARTIALLY
		);
		
		return in_array($dbBatchJob->getStatus(), $closedStatusList);
	}
	
	public static function addintegrationJob($objectType, $objectId, kIntegrationJobData $data) 
	{
		$partnerId = kCurrentContext::getCurrentPartnerId();
		
		$providerType = $data->getProviderType();
		$integrationProvider = KalturaPluginManager::loadObject('IIntegrationProvider', $providerType);

		if(!$integrationProvider || !$integrationProvider->validatePermissions($partnerId))
		{
			KalturaLog::err("partner $partnerId not permitted with provider type $providerType");
			return false;
		}
		
		$batchJob = new BatchJob();
		$batchJob->setPartnerId($partnerId);
		$batchJob->setObjectType($objectType);
		$batchJob->setObjectId($objectId);
		
		if($objectType == BatchJobObjectType::ENTRY)
		{
			$batchJob->setEntryId($objectId);
		}
		elseif($objectType == BatchJobObjectType::ASSET)
		{
			$asset = assetPeer::retrieveById($objectId);
			if($asset)
				$batchJob->setEntryId($asset->getEntryId());
		}
		
		$batchJob->setStatus(BatchJob::BATCHJOB_STATUS_DONT_PROCESS);
		
		$jobType = IntegrationPlugin::getBatchJobTypeCoreValue(IntegrationBatchJobType::INTEGRATION);
		$batchJob = kJobsManager::addJob($batchJob, $data, $jobType, $providerType);
		
		if($integrationProvider->shouldSendCallBack())
		{
			$jobId = $batchJob->getId();
			$ks = self::generateKs($partnerId, $jobId);
			$dcParams = kDataCenterMgr::getCurrentDc();
			$dcUrl = $dcParams["url"];

			$callBackUrl = $dcUrl;
			$callBackUrl .= "/api_v3/index.php/service/integration_integration/action/notify";
			$callBackUrl .= "/id/$jobId/ks/$ks";

			$data = $batchJob->getData();
			$data->setCallbackNotificationUrl($callBackUrl);
			$batchJob->setData($data);
		}
		
		return kJobsManager::updateBatchJob($batchJob, BatchJob::BATCHJOB_STATUS_PENDING);
	}
	
	/**
	 * @return string
	 */
	public static function generateKs($partnerId, $tokenPrefix)
	{
		$partner = PartnerPeer::retrieveByPK($partnerId);
		$userSecret = $partner->getSecret();
		
		//actionslimit:1
		$privileges = kSessionBase::PRIVILEGE_SET_ROLE . ":" . self::EXTERNAL_INTEGRATION_SERVICES_ROLE_NAME;
		$privileges .= "," . kSessionBase::PRIVILEGE_ACTIONS_LIMIT . ":1";
		
		$dcParams = kDataCenterMgr::getCurrentDc();
		$token = $dcParams["secret"];
		$additionalData = md5($tokenPrefix . $token);
		
		$ks = "";
		$creationSucces = kSessionUtils::startKSession ($partnerId, $userSecret, "", $ks, self::THREE_DAYS_IN_SECONDS, KalturaSessionType::USER, "", $privileges, null,$additionalData);
		if ($creationSucces >= 0 )
				return $ks;
		
		return false;
	}
}
