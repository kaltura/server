<?php
/**
 * batch service lets you handle different batch process from remote machines.
 * As oppesed to other ojects in the system, locking mechanism is critical in this case.
 * For this reason the GetExclusiveXX, UpdateExclusiveXX and FreeExclusiveXX actions are important for the system's intergity.
 * In general - updating batch object should be done only using the UpdateExclusiveXX which in turn can be called only after 
 * acuiring a batch objet properly (using  GetExclusiveXX).
 * If an object was aquired and should be returned to the pool in it's initial state - use the FreeExclusiveXX action 
 *
 *	Terminology:
 *		LocationId
 *		ServerID
 *		ParternGroups 
 * 
 * @service batchcontrol
 * @package api
 * @subpackage services
 */
class BatchControlService extends KalturaBaseService 
{
	// use initService to add a peer to the partner filter
	/**
	 * @ignore
	 */
	public function initService($serviceName, $actionName)
	{
		parent::initService($serviceName, $actionName);
//		parent::applyPartnerFilterForClass ( new BatchJobPeer() ); 	
//		parent::applyPartnerFilterForClass ( new notificationPeer() );
	}
	

		
	
// --------------------------------- scheduler support functions 	--------------------------------- //

		
	
	/**
	 * batch reportStatus action saves the a status attribute from a remote scheduler and returns pending commands for the scheduler
	 * 
	 * @action reportStatus
	 * @param KalturaScheduler $scheduler The scheduler
	 * @param KalturaSchedulerStatusArray $schedulerStatuses A scheduler status array
	 * @param KalturaWorkerQueueFilterArray $workerQueueFilters Filters list to get queues
	 * @return KalturaSchedulerStatusResponse
	 */
	function reportStatusAction(KalturaScheduler $scheduler, KalturaSchedulerStatusArray $schedulerStatuses, KalturaWorkerQueueFilterArray $workerQueueFilters)
	{
		$schedulerDb = $this->getOrCreateScheduler($scheduler);
		$schedulerChanged = false;
		
		// saves the statuses to the DB
		foreach($schedulerStatuses as $schedulerStatus)
		{
			$schedulerStatus->schedulerId = $schedulerDb->getId();
			$schedulerStatus->schedulerConfiguredId = $scheduler->configuredId;
			
			if($schedulerStatus->workerConfiguredId)
			{
				$worker = $this->getOrCreateWorker($schedulerDb, $schedulerStatus->workerConfiguredId, $schedulerStatus->workerType);
				$worker->setStatus($schedulerStatus->type, $schedulerStatus->value);
				$worker->save();
				$schedulerStatus->workerId = $worker->getId();
			}
			else
			{
				$schedulerChanged = true;
				$schedulerDb->setStatus($schedulerStatus->type, $schedulerStatus->value);
			}
			
			$schedulerStatusDb = new SchedulerStatus();
			$schedulerStatus->toObject($schedulerStatusDb);
			$schedulerStatusDb->save();
		}
		if($schedulerChanged)
			$schedulerDb->save();
		
		
		// creates a response
		$schedulerStatusResponse = new KalturaSchedulerStatusResponse();
		
		// gets the control pannel commands
		$c = new Criteria();
		$c->add(ControlPanelCommandPeer::SCHEDULER_ID, $schedulerDb->getId());
		$c->add(ControlPanelCommandPeer::TYPE, KalturaControlPanelCommandType::CONFIG, Criteria::NOT_EQUAL);
		$c->add(ControlPanelCommandPeer::STATUS, KalturaControlPanelCommandStatus::PENDING);
		$commandsList = ControlPanelCommandPeer::doSelect($c);
		foreach($commandsList as $command)
		{
			$command->setStatus(KalturaControlPanelCommandStatus::HANDLED);
			$command->save();
		}
		$schedulerStatusResponse->controlPanelCommands = KalturaControlPanelCommandArray::fromControlPanelCommandArray($commandsList);
		
		// gets new configs
		$c = new Criteria();
		$c->add(SchedulerConfigPeer::SCHEDULER_ID, $schedulerDb->getId());
		$c->add(SchedulerConfigPeer::COMMAND_STATUS, KalturaControlPanelCommandStatus::PENDING);
		$configList = SchedulerConfigPeer::doSelect($c);
		foreach($configList as $config)
		{
			$config->setCommandStatus(KalturaControlPanelCommandStatus::HANDLED);
			$config->save();
		}
		$schedulerStatusResponse->schedulerConfigs = KalturaSchedulerConfigArray::fromSchedulerConfigArray($configList);
		
		// gets queues length
		$schedulerStatusResponse->queuesStatus = new KalturaBatchQueuesStatusArray();
		foreach($workerQueueFilters as $workerQueueFilter)
		{
			$dbJobType = kPluginableEnumsManager::apiToCore('BatchJobType', $workerQueueFilter->jobType);
			$filter = $workerQueueFilter->filter->toFilter($dbJobType);
			
			$batchQueuesStatus = new KalturaBatchQueuesStatus();
			$batchQueuesStatus->jobType = $workerQueueFilter->jobType;
			$batchQueuesStatus->workerId = $workerQueueFilter->workerId;
			$batchQueuesStatus->size = kBatchManager::getQueueSize($workerQueueFilter->schedulerId, $workerQueueFilter->workerId, $dbJobType, $filter);
			
			$schedulerStatusResponse->queuesStatus[] = $batchQueuesStatus;
		}
		
		return $schedulerStatusResponse;
	}
	
	
	/**
	 * batch getOrCreateScheduler returns a scheduler by name, create it if doesnt exist
	 * 
	 * @param KalturaScheduler $scheduler
	 * @return Scheduler
	 */
	private function getOrCreateScheduler(KalturaScheduler $scheduler)
	{
		$c = new Criteria();
		$c->add ( SchedulerPeer::CONFIGURED_ID, $scheduler->configuredId);
		$schedulerDb = SchedulerPeer::doSelectOne($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		
		if($schedulerDb)
		{
			if(strlen($schedulerDb->getHost()) && $schedulerDb->getHost() != $scheduler->host)
				throw new KalturaAPIException(KalturaErrors::SCHEDULER_HOST_CONFLICT, $scheduler->configuredId, $scheduler->host, $schedulerDb->getHost());
			
			if($schedulerDb->getName() != $scheduler->name || $schedulerDb->getHost() != $scheduler->host)
			{
				$schedulerDb->setName($scheduler->name);
				$schedulerDb->setHost($scheduler->host);
				$schedulerDb->save();
			}
			
			return $schedulerDb;
		}
			
		$schedulerDb = new Scheduler();
		$schedulerDb->setLastStatus(time());
		$schedulerDb->setName($scheduler->name);
		$schedulerDb->setHost($scheduler->host);
		$schedulerDb->setConfiguredId($scheduler->configuredId);
		$schedulerDb->setDescription('');
		
		$schedulerDb->save();
		
		return $schedulerDb;
	}
	
	
	/**
	 * batch getOrCreateWorker returns a worker by name, create it if doesnt exist
	 * 
	 * @param Scheduler $scheduler The scheduler object
	 * @param int $workerConfigId The worker configured id
	 * @param KalturaBatchJobType $workerType The type of the remote worker
	 * @param string $workerName The name of the remote worker
	 * @return Worker
	 */
	private function getOrCreateWorker(Scheduler $scheduler, $workerConfigId, $workerType = null, $workerName = null)
	{
		$c = new Criteria();
		$c->add ( SchedulerWorkerPeer::SCHEDULER_CONFIGURED_ID, $scheduler->getConfiguredId());
		$c->add ( SchedulerWorkerPeer::CONFIGURED_ID, $workerConfigId);
		$workerDb = SchedulerWorkerPeer::doSelectOne($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		
		if($workerDb)
		{
			$shouldSave = false;
			
			if(!is_null($workerName) && $workerDb->getName() != $workerName)
			{
				$workerDb->setName($workerName);
				$shouldSave = true;
			}
			
			if(!is_null($workerType) && $workerDb->getType() != $workerType)
			{
				$workerDb->setType($workerType);
				$shouldSave = true;
			}
			
			if($shouldSave)
				$workerDb->save();
			
			return $workerDb;
		}
			
		$workerDb = new SchedulerWorker();
		$workerDb->setLastStatus(time());
		$workerDb->setCreatedBy("Scheduler: " . $scheduler->getName());
		$workerDb->setUpdatedBy("Scheduler: " . $scheduler->getName());
		$workerDb->setSchedulerId($scheduler->getId());
		$workerDb->setSchedulerConfiguredId($scheduler->getConfiguredId());
		$workerDb->setConfiguredId($workerConfigId);
		$workerDb->setDescription('');
		
		if(!is_null($workerType))
			$workerDb->setType($workerType);
		
		if(!is_null($workerName))
			$workerDb->setName($workerName);
		
		$workerDb->save();
		
		return $workerDb;
	}
	
	
	/**
	 * batch configLoaded action saves the configuration as loaded by a remote scheduler
	 * 
	 * @action configLoaded
	 * @param KalturaScheduler $scheduler The remote scheduler
	 * @param string $configParam The parameter that was loaded
	 * @param string $configValue The value that was loaded
	 * @param string $configParamPart The parameter part that was loaded
	 * @param int $workerConfigId The id of the job that the configuration refers to, not mandatory if the configuration refers to the scheduler
	 * @param string $workerName The name of the job that the configuration refers to, not mandatory if the configuration refers to the scheduler 
	 * @return KalturaSchedulerConfig
	 */
	function configLoadedAction(KalturaScheduler $scheduler, $configParam, $configValue, $configParamPart = null, $workerConfigId = null, $workerName = null)
	{
		$schedulerDb = $this->getOrCreateScheduler($scheduler);
		
		
		// saves the loaded config to the DB
		$configDb = new SchedulerConfig();
		$configDb->setSchedulerId($schedulerDb->getId());
		$configDb->setSchedulerName($scheduler->name);
		$configDb->setSchedulerConfiguredId($scheduler->configuredId);
		
		$configDb->setVariable($configParam);
		$configDb->setVariablePart($configParamPart);
		$configDb->setValue($configValue);
		
		if($workerConfigId)
		{
			$worker = $this->getOrCreateWorker($schedulerDb, $workerConfigId, null, $workerName);
			
			$configDb->setWorkerId($worker->getId());
			$configDb->setWorkerConfiguredId($workerConfigId);
			$configDb->setWorkerName($workerName);
		}
		
		$configDb->save();
		
		$config = new KalturaSchedulerConfig();
		$config->fromObject($configDb);
		return $config;
	}
	
// --------------------------------- scheduler support functions 	--------------------------------- //

	
	
	
// --------------------------------- control panel functions 	--------------------------------- //

	
	/**
	 * batch stop action stops a scheduler
	 * 
	 * @action stopScheduler
	 * @param int $schedulerId The id of the remote scheduler location
	 * @param int $adminId The id of the admin that called the stop
	 * @param string $cause The reason it was stopped
	 * @return KalturaControlPanelCommand
	 */
	function stopSchedulerAction($schedulerId, $adminId, $cause)
	{
		$adminDb = kuserPeer::retrieveByPK($adminId);
		$schedulerDb = SchedulerPeer::retrieveByPK($schedulerId);
		if(!$schedulerDb)
			throw new KalturaAPIException(KalturaErrors::SCHEDULER_NOT_FOUND, $schedulerId);
	
		$description = "Stop " . $schedulerDb->getName();
			
		// check if the same command already sent and not done yet
		$c = new Criteria();
		$c->add(ControlPanelCommandPeer::STATUS, array(KalturaControlPanelCommandStatus::PENDING, KalturaControlPanelCommandStatus::HANDLED), Criteria::IN);
		$c->add(ControlPanelCommandPeer::SCHEDULER_ID, $schedulerId);
		$c->add(ControlPanelCommandPeer::TYPE, KalturaControlPanelCommandType::STOP);
		$c->add(ControlPanelCommandPeer::TARGET_TYPE, KalturaControlPanelCommandTargetType::SCHEDULER);
		$commandExists = ControlPanelCommandPeer::doCount($c);
		if($commandExists > 0)
			throw new KalturaAPIException(KalturaErrors::COMMAND_ALREADY_PENDING);
		
		// saves the command to the DB
		$commandDb = new ControlPanelCommand();
		$commandDb->setSchedulerId($schedulerId);
		$commandDb->setSchedulerConfiguredId($schedulerDb->getConfiguredId());
		$commandDb->setCreatedById($adminId);
		$commandDb->setType(KalturaControlPanelCommandType::STOP);
		$commandDb->setStatus(KalturaControlPanelCommandStatus::PENDING);
		$commandDb->setDescription($description);
		$commandDb->setTargetType(KalturaControlPanelCommandTargetType::SCHEDULER);

		if($adminDb)
			$commandDb->setCreatedBy($adminDb->getName());
			
		$commandDb->save();
		
		$command = new KalturaControlPanelCommand();
		$command->fromObject($commandDb);
		return $command;
	}	
	
	/**
	 * batch stop action stops a worker
	 * 
	 * @action stopWorker
	 * @param int $workerId The id of the job to be stopped
	 * @param int $adminId The id of the admin that called the stop
	 * @param string $cause The reason it was stopped
	 * @return KalturaControlPanelCommand
	 */
	function stopWorkerAction($workerId, $adminId, $cause)
	{
		$adminDb = kuserPeer::retrieveByPK($adminId);
		
		$workerDb = SchedulerWorkerPeer::retrieveByPK($workerId);
		if(!$workerDb)
			throw new KalturaAPIException(KalturaErrors::WORKER_NOT_FOUND, $workerId);
		
		$workerName = $workerDb->getName();
		$schedulerId = $workerDb->getSchedulerId();
		
		$schedulerDb = SchedulerPeer::retrieveByPK($schedulerId);
		if(!$schedulerDb)
			throw new KalturaAPIException(KalturaErrors::SCHEDULER_NOT_FOUND, $schedulerId);
		
		$schedulerName = $schedulerDb->getName();
		$description = "Stop $workerName on $schedulerName";
			
		// check if the same command already sent and not done yet
		$c = new Criteria();
		$c->add(ControlPanelCommandPeer::STATUS, array(KalturaControlPanelCommandStatus::PENDING, KalturaControlPanelCommandStatus::HANDLED), Criteria::IN);
		$c->add(ControlPanelCommandPeer::SCHEDULER_ID, $schedulerId);
		$c->add(ControlPanelCommandPeer::TYPE, KalturaControlPanelCommandType::STOP);
		$c->add(ControlPanelCommandPeer::TARGET_TYPE, KalturaControlPanelCommandTargetType::JOB);
		$c->add(ControlPanelCommandPeer::WORKER_ID, $workerId);
		$commandExists = ControlPanelCommandPeer::doCount($c);
		if($commandExists > 0)
			throw new KalturaAPIException(KalturaErrors::COMMAND_ALREADY_PENDING);
		
		// saves the command to the DB
		$commandDb = new ControlPanelCommand();
		$commandDb->setSchedulerId($schedulerId);
		$commandDb->setSchedulerConfiguredId($schedulerDb->getConfiguredId());
		$commandDb->setWorkerId($workerId);
		$commandDb->setWorkerConfiguredId($workerDb->getConfiguredId());
		$commandDb->setWorkerName($workerName);
		$commandDb->setCreatedById($adminId);
		$commandDb->setType(KalturaControlPanelCommandType::STOP);
		$commandDb->setStatus(KalturaControlPanelCommandStatus::PENDING);
		$commandDb->setDescription($description);
		$commandDb->setTargetType(KalturaControlPanelCommandTargetType::JOB);
		$commandDb->setCause($cause);

		if($adminDb)
			$commandDb->setCreatedBy($adminDb->getName());
				
		$commandDb->save();
		
		$command = new KalturaControlPanelCommand();
		$command->fromObject($commandDb);
		return $command;
	}	
	
	/**
	 * batch kill action forces stop og a batch on a remote scheduler
	 * 
	 * @action kill
	 * @param int $workerId The id of the job to be stopped
	 * @param int $batchIndex The index of the batch job process to be stopped
	 * @param int $adminId The id of the admin that called the stop
	 * @param string $cause The reason it was stopped
	 * @return KalturaControlPanelCommand
	 */
	function killAction($workerId, $batchIndex, $adminId, $cause)
	{
		$adminDb = kuserPeer::retrieveByPK($adminId);
		
		$workerDb = SchedulerWorkerPeer::retrieveByPK($workerId);
		if(!$workerDb)
			throw new KalturaAPIException(KalturaErrors::WORKER_NOT_FOUND, $workerId);
		
		$workerName = $workerDb->getName();
		$schedulerId = $workerDb->getSchedulerId();
		
		$schedulerDb = SchedulerPeer::retrieveByPK($schedulerId);
		if(!$schedulerDb)
			throw new KalturaAPIException(KalturaErrors::SCHEDULER_NOT_FOUND, $schedulerId);
		
		$schedulerName = $schedulerDb->getName();
			
		$description = "Stop $workerName on $schedulerName";
		if(is_null($workerName))
			$description = "Stop $schedulerName";
			
		// check if the same command already sent and not done yet
		$c = new Criteria();
		$c->add(ControlPanelCommandPeer::STATUS, array(KalturaControlPanelCommandStatus::PENDING, KalturaControlPanelCommandStatus::HANDLED), Criteria::IN);
		$c->add(ControlPanelCommandPeer::SCHEDULER_ID, $schedulerId);
		$c->add(ControlPanelCommandPeer::WORKER_ID, $workerId);
		$c->add(ControlPanelCommandPeer::WORKER_NAME, $workerName);
		$c->add(ControlPanelCommandPeer::BATCH_INDEX, $batchIndex);
		$c->add(ControlPanelCommandPeer::TYPE, KalturaControlPanelCommandType::KILL);
		$c->add(ControlPanelCommandPeer::TARGET_TYPE, KalturaControlPanelCommandTargetType::BATCH);
		$commandExists = ControlPanelCommandPeer::doCount($c);
		if($commandExists > 0)
			throw new KalturaAPIException(KalturaErrors::COMMAND_ALREADY_PENDING);
		
		// saves the command to the DB
		$commandDb = new ControlPanelCommand();
		$commandDb->setSchedulerId($schedulerId);
		$commandDb->setSchedulerConfiguredId($schedulerDb->getConfiguredId());
		$commandDb->setWorkerId($workerId);
		$commandDb->setWorkerConfiguredId($workerDb->getConfiguredId());
		$commandDb->setWorkerName($workerName);
		$commandDb->setBatchIndex($batchIndex);
		$commandDb->setCreatedById($adminId);
		$commandDb->setType(KalturaControlPanelCommandType::KILL);
		$commandDb->setStatus(KalturaControlPanelCommandStatus::PENDING);
		$commandDb->setDescription($description);
		$commandDb->setTargetType(KalturaControlPanelCommandTargetType::BATCH);
				
		if($adminDb)
			$commandDb->setCreatedBy($adminDb->getName());
				
		$commandDb->save();
		
		$command = new KalturaControlPanelCommand();
		$command->fromObject($commandDb);
		return $command;
	}	
	
	/**
	 * batch start action starts a job
	 * 
	 * @action startWorker
	 * @param int $workerId The id of the job to be started
	 * @param int $adminId The id of the admin that called the start
	 * @param string $cause The reason it was started 
	 * @return KalturaControlPanelCommand
	 */
	function startWorkerAction($workerId, $adminId, $cause = null)
	{
		$adminDb = kuserPeer::retrieveByPK($adminId);
		
		$workerDb = SchedulerWorkerPeer::retrieveByPK($workerId);
		if(!$workerDb)
			throw new KalturaAPIException(KalturaErrors::WORKER_NOT_FOUND, $workerId);
		
		$workerName = $workerDb->getName();
		$schedulerId = $workerDb->getSchedulerId();
		
		$schedulerDb = SchedulerPeer::retrieveByPK($schedulerId);
		if(!$schedulerDb)
			throw new KalturaAPIException(KalturaErrors::SCHEDULER_NOT_FOUND, $schedulerId);
		
		$schedulerName = $schedulerDb->getName();
			
		$description = "Start $workerName on $schedulerName";
			
		// check if the same command already sent and not done yet
		$c = new Criteria();
		$c->add(ControlPanelCommandPeer::STATUS, array(KalturaControlPanelCommandStatus::PENDING, KalturaControlPanelCommandStatus::HANDLED), Criteria::IN);
		$c->add(ControlPanelCommandPeer::SCHEDULER_ID, $schedulerId);
		$c->add(ControlPanelCommandPeer::TYPE, KalturaControlPanelCommandType::START);
		$c->add(ControlPanelCommandPeer::TARGET_TYPE, KalturaControlPanelCommandTargetType::JOB);
		$c->add(ControlPanelCommandPeer::WORKER_ID, $workerId);
		$commandExists = ControlPanelCommandPeer::doCount($c);
		if($commandExists > 0)
			throw new KalturaAPIException(KalturaErrors::COMMAND_ALREADY_PENDING);
	
		// saves the command to the DB
		$commandDb = new ControlPanelCommand();
		$commandDb->setSchedulerId($schedulerId);
		$commandDb->setSchedulerConfiguredId($schedulerDb->getConfiguredId());
		$commandDb->setCreatedById($adminId);
		$commandDb->setType(KalturaControlPanelCommandType::START);
		$commandDb->setStatus(KalturaControlPanelCommandStatus::PENDING);
		$commandDb->setDescription($description);
		$commandDb->setTargetType(KalturaControlPanelCommandTargetType::JOB);
		$commandDb->setWorkerId($workerId);
		$commandDb->setWorkerConfiguredId($workerDb->getConfiguredId());
		$commandDb->setWorkerName($workerName);
			
		if($adminDb)
			$commandDb->setCreatedBy($adminDb->getName());
				
		$commandDb->save();
		
		$command = new KalturaControlPanelCommand();
		$command->fromObject($commandDb);
		return $command;
	}
	
	/**
	 * batch sets a configuration parameter to be loaded by a scheduler
	 * 
	 * @action setSchedulerConfig
	 * @param int $schedulerId The id of the remote scheduler location
	 * @param int $adminId The id of the admin that called the setConfig
	 * @param string $configParam The parameter to be set
	 * @param string $configValue The value to be set
	 * @param string $configParamPart The parameter part to be set - for additional params
	 * @param string $cause The reason it was changed
	 * @return KalturaControlPanelCommand
	 */
	function setSchedulerConfigAction($schedulerId, $adminId, $configParam, $configValue, $configParamPart = null, $cause = null)
	{
		$adminDb = kuserPeer::retrieveByPK($adminId);
		
		$schedulerDb = SchedulerPeer::retrieveByPK($schedulerId);
		if(!$schedulerDb)
			throw new KalturaAPIException(KalturaErrors::SCHEDULER_NOT_FOUND, $schedulerId);
		
		$schedulerName = $schedulerDb->getName();
		
		$description = "Configure $configParam on $schedulerName";
		if(!is_null($configParamPart))
			$description = "Configure $configParam.$configParamPart on $schedulerName";
			
		// saves the command to the DB
		$commandDb = new ControlPanelCommand();
		$commandDb->setSchedulerId($schedulerId);
		$commandDb->setSchedulerConfiguredId($schedulerDb->getConfiguredId());
		$commandDb->setCreatedById($adminId);
		$commandDb->setType(KalturaControlPanelCommandType::CONFIG);
		$commandDb->setStatus(KalturaControlPanelCommandStatus::PENDING);
		$commandDb->setDescription($description);
		$commandDb->setTargetType(KalturaControlPanelCommandTargetType::SCHEDULER);
		$commandDb->setCause($cause);
			
		if($adminDb)
			$commandDb->setCreatedBy($adminDb->getName());
				
		$commandDb->save();
		
		// saves the new config to the DB
		$configDb = new SchedulerConfig();
		$configDb->setSchedulerId($schedulerId);
		$configDb->setSchedulerConfiguredId($schedulerDb->getConfiguredId());
		$configDb->setCommandId($commandDb->getId());
		$configDb->setCommandStatus(KalturaControlPanelCommandStatus::PENDING);
		$configDb->setSchedulerName($schedulerName);
		$configDb->setVariable($configParam);
		$configDb->setVariablePart($configParamPart);
		$configDb->setValue($configValue);
		
		if($adminDb)
			$configDb->setCreatedBy($adminDb->getName());
				
		$configDb->save();
		
		$command = new KalturaControlPanelCommand();
		$command->fromObject($commandDb);
		return $command;
	}
	
	/**
	 * batch sets a configuration parameter to be loaded by a worker
	 * 
	 * @action setWorkerConfig
	 * @param int $workerId The id of the job to be configured
	 * @param int $adminId The id of the admin that called the setConfig
	 * @param string $configParam The parameter to be set
	 * @param string $configValue The value to be set
	 * @param string $configParamPart The parameter part to be set - for additional params
	 * @param string $cause The reason it was changed
	 * @return KalturaControlPanelCommand
	 */
	function setWorkerConfigAction($workerId, $adminId, $configParam, $configValue, $configParamPart = null, $cause = null)
	{
		$adminDb = kuserPeer::retrieveByPK($adminId);
		
		$workerDb = SchedulerWorkerPeer::retrieveByPK($workerId);
		if(!$workerDb)
			throw new KalturaAPIException(KalturaErrors::WORKER_NOT_FOUND, $workerId);
		
		$workerName = $workerDb->getName();
		$schedulerId = $workerDb->getSchedulerId();
		
		$schedulerDb = SchedulerPeer::retrieveByPK($schedulerId);
		if(!$schedulerDb)
			throw new KalturaAPIException(KalturaErrors::SCHEDULER_NOT_FOUND, $schedulerId);
		
		$schedulerName = $schedulerDb->getName();
		
		$description = "Configure $configParam on $schedulerName";
		if(!is_null($configParamPart))
			$description = "Configure $configParam.$configParamPart on $schedulerName";
			
		// saves the command to the DB
		$commandDb = new ControlPanelCommand();
		$commandDb->setSchedulerId($schedulerId);
		$commandDb->setSchedulerConfiguredId($schedulerDb->getConfiguredId());
		$commandDb->setCreatedById($adminId);
		$commandDb->setType(KalturaControlPanelCommandType::CONFIG);
		$commandDb->setStatus(KalturaControlPanelCommandStatus::PENDING);
		$commandDb->setDescription($description);
		$commandDb->setWorkerId($workerId);
		$commandDb->setWorkerConfiguredId($workerDb->getConfiguredId());
		$commandDb->setWorkerName($workerName);
		$commandDb->setTargetType(KalturaControlPanelCommandTargetType::JOB);
		
		if($adminDb)
			$commandDb->setCreatedBy($adminDb->getName());
				
		$commandDb->setCause($cause);
			
		$commandDb->save();
		
		// saves the new config to the DB
		$configDb = new SchedulerConfig();
		$configDb->setSchedulerId($schedulerId);
		$configDb->setSchedulerConfiguredId($schedulerDb->getConfiguredId());
		$configDb->setCommandId($commandDb->getId());
		$configDb->setCommandStatus(KalturaControlPanelCommandStatus::PENDING);
		$configDb->setSchedulerName($schedulerName);
		$configDb->setVariable($configParam);
		$configDb->setVariablePart($configParamPart);
		$configDb->setValue($configValue);
		$configDb->setWorkerId($workerId);
		$configDb->setWorkerConfiguredId($workerDb->getConfiguredId());
		$configDb->setWorkerName($workerName);
		
		if($adminDb)
			$configDb->setCreatedBy($adminDb->getName());
		
		$configDb->save();
		
		$command = new KalturaControlPanelCommand();
		$command->fromObject($commandDb);
		return $command;
	}
	
	/**
	 * batch setCommandResult action saves the results of a command as recieved from a remote scheduler
	 * 
	 * @action setCommandResult
	 * @param int $commandId The id of the command
	 * @param KalturaControlPanelCommandStatus $status The status of the command
	 * @param int $timestamp The time that the command performed
	 * @param string $errorDescription The description, important for failed commands
	 * @return KalturaControlPanelCommand
	 */
	function setCommandResultAction($commandId, $status, $errorDescription = null)
	{
		// find the command
		$commandDb = ControlPanelCommandPeer::retrieveByPK($commandId);
		if (!$commandDb)
			throw new KalturaAPIException(KalturaErrors::COMMAND_NOT_FOUND, $commandId);
		
		// save the results to the DB
		$commandDb->setStatus($status);
		if(!is_null($errorDescription))
			$commandDb->setErrorDescription($errorDescription);
		$commandDb->save();

		// if is config, update the config status
		if($commandDb->getType() == KalturaControlPanelCommandType::CONFIG)
		{
			$c = new Criteria();
			$c->add ( SchedulerConfigPeer::COMMAND_ID, $commandId);
			$configDb = SchedulerConfigPeer::doSelectOne($c);
			
			if($configDb)
			{
				$configDb->setCommandStatus($status);
				$configDb->save();
			}
		}
		
		$command = new KalturaControlPanelCommand();
		$command->fromObject($commandDb);
		return $command;
	}

	/**
	 * list batch control commands
	 * 
	 * @action listCommands
	 * @param KalturaControlPanelCommandFilter $filter
	 * @param KalturaFilterPager $pager  
	 * @return KalturaControlPanelCommandListResponse
	 */
	function listCommandsAction(KalturaControlPanelCommandFilter $filter = null, KalturaFilterPager $pager = null)
	{
		if (!$filter)
			$filter = new KalturaControlPanelCommandFilter();
			
		$controlPanelCommandFilter = new ControlPanelCommandFilter();
		$filter->toObject($controlPanelCommandFilter);
		
		$c = new Criteria();
		
		$controlPanelCommandFilter->attachToCriteria($c);
		
		if ($pager )	
			$pager->attachToCriteria($c);
		
		$count = ControlPanelCommandPeer::doCount($c);
		$list = ControlPanelCommandPeer::doSelect($c);

		$newList = KalturaControlPanelCommandArray::fromControlPanelCommandArray($list);
		
		$response = new KalturaControlPanelCommandListResponse();
		$response->objects = $newList;
		$response->totalCount = $count;
		
		return $response;
	}
	
	/**
	 * batch getCommand action returns the command with its current status
	 * 
	 * @action getCommand
	 * @param int $commandId The id of the command
	 * @return KalturaControlPanelCommand
	 */
	function getCommandAction($commandId)
	{
		// finds command in the DB
		$commandDb = ControlPanelCommandPeer::retrieveByPK($commandId);
		if (!$commandDb)
			throw new KalturaAPIException(KalturaErrors::COMMAND_NOT_FOUND, $commandId);
		
		// returns the command
		$command = new KalturaControlPanelCommand();
		$command->fromObject($commandDb);
		return $command;
	}

	/**
	 * list all Schedulers
	 * 
	 * @action listSchedulers
	 * @return KalturaSchedulerListResponse
	 */
	function listSchedulersAction()
	{
		$c = new Criteria();
		$count = SchedulerPeer::doCount($c, false, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		$list = SchedulerPeer::doSelect($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		$newList = KalturaSchedulerArray::fromSchedulerArray($list );
		
		$response = new KalturaSchedulerListResponse();
		$response->objects = $newList;
		$response->totalCount = $count;
		
		return $response;
	}

	/**
	 * list all Workers
	 * 
	 * @action listWorkers
	 * @return KalturaSchedulerWorkerListResponse
	 */
	function listWorkersAction()
	{
		$c = new Criteria();
		$count = SchedulerWorkerPeer::doCount($c, false, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		$list = SchedulerWorkerPeer::doSelect($c, myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		$newList = KalturaSchedulerWorkerArray::fromSchedulerWorkerArray($list);
		
		$response = new KalturaSchedulerWorkerListResponse();
		$response->objects = $newList;
		$response->totalCount = $count;
		
		return $response;
	}

	/**
	 * batch getFullStatus action returns the status of all schedulers and queues
	 * 
	 * @action getFullStatus
	 * @return KalturaControlPanelCommand
	 */
	function getFullStatusAction()
	{
		$response = new KalturaFullStatusResponse();
		
		// gets queues length
//		$c = new Criteria();
//		$c->add(BatchJobPeer::STATUS, array(KalturaBatchJobStatus::PENDING, KalturaBatchJobStatus::RETRY), Criteria::IN);
//		$c->addGroupByColumn(BatchJobPeer::JOB_TYPE);
//		$c->addSelectColumn('AVG(DATEDIFF(NOW(),' . BatchJobPeer::CREATED_AT . '))');
		$queueList = BatchJobPeer::doQueueStatus(myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2));
		$response->queuesStatus = KalturaBatchQueuesStatusArray::fromBatchQueuesStatusArray($queueList);
		
		$response->schedulers = KalturaSchedulerArray::statusFromSchedulerArray(SchedulerPeer::doSelect(new Criteria(), myDbHelper::getConnection(myDbHelper::DB_HELPER_CONN_PROPEL2)));
		
		return $response;
	}
	
// --------------------------------- control panel functions 	--------------------------------- //	
	

}
?>