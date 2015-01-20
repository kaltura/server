<?php 
/**
 * @package plugins.activitiBusinessProcessNotification
 */
class kActivitiBusinessProcessProvider extends kBusinessProcessProvider
{
	/**
	 * @var KalturaActivitiBusinessProcessServer
	 */
	protected $server;
	
	/**
	 * @var ActivitiClient
	 */
	protected $client;

	public function __construct($server)
	{
		$this->server = $server;
		
		$this->client = new ActivitiClient();
		$this->client->setUrl($this->server->host, $this->server->port, $this->server->protocol);
		$this->client->setCredentials($this->server->username, $this->server->password);
	}
	
	/* (non-PHPdoc)
	 * @see kBusinessProcessProvider::listBusinessProcesses()
	 */
	public function listBusinessProcesses()
	{
		$size = 100;
		$start = 0;
		$processes = $this->client->processDefinitions->listOfProcessDefinitions($size);
		
		$ret = array();
		while($processes)
		{
			KalturaLog::debug('processes [' . print_r($processes, true) . ']');
			foreach($processes->getData() as $process)
			{
				/* @var $process ActivitiListOfProcessDefinitionsResponseData */
				$ret[$process->getKey()] = $process->getName();
			}
			
			if(($processes->getStart() + $processes->getSize()) < $processes->getTotal())
			{
				$start += $size;
				$processes = $this->client->processDefinitions->listOfProcessDefinitions($size, $start);
			}
			else 
			{
				$processes = false;
			}
		}
		return $ret;
	}

	/* (non-PHPdoc)
	 * @see kBusinessProcessProvider::startBusinessProcess()
	 */
	public function startBusinessProcess($processId, array $variables)
	{
		$startVariables = array();
		foreach($variables as $name => $value)
		{
			$variable = new ActivitiStartProcessInstanceRequestVariable();
			$variable->setName($name);
			$variable->setValue($value);
			$startVariables[] = $variable;
		}
		
		$response = $this->client->processInstances->startProcessInstance(null, null, $startVariables, $processId);
		if($response)
			return $response->getId();
			
		return null;
	}
	
	/* (non-PHPdoc)
	 * @see kBusinessProcessProvider::abortCase()
	 */
	public function abortCase($caseId)
	{
		$this->client->processInstances->deleteProcessInstance($caseId);
	}

	/* (non-PHPdoc)
	 * @see kBusinessProcessProvider::signalCase()
	 */
	public function signalCase($caseId, $eventId, $message, array $variables = array())
	{
		$messageVariables = array();
		foreach($variables as $name => $value)
		{
			$variable = new ActivitiStartProcessInstanceRequestVariable();
			$variable->setName($name);
			$variable->setValue($value);
			$messageVariables[] = $variable;
		}
		
		$processInstances = $this->client->executions->queryExecutions($caseId, null, $messageVariables);
		
		$action = 'messageEventReceived';
		
		foreach($processInstances->getData() as $processInstance)
		{
			/* @var $processInstance ActivitiQueryExecutionsResponseData */
			if($processInstance->getActivityid() === $eventId)
			{
				$this->client->executions->executeAnActionOnAnExecution($processInstance->getId(), $action, null, null, $message);
			}
		}
	}
}