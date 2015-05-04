<?php

require_once(__DIR__ . '/ActivitiClientBase.php');
require_once(__DIR__ . '/services/ActivitiDeploymentService.php');
require_once(__DIR__ . '/services/ActivitiProcessDefinitionsService.php');
require_once(__DIR__ . '/services/ActivitiModelsService.php');
require_once(__DIR__ . '/services/ActivitiProcessInstancesService.php');
require_once(__DIR__ . '/services/ActivitiExecutionsService.php');
require_once(__DIR__ . '/services/ActivitiTasksService.php');
require_once(__DIR__ . '/services/ActivitiHistoryService.php');
require_once(__DIR__ . '/services/ActivitiFormsService.php');
require_once(__DIR__ . '/services/ActivitiDatabaseTablesService.php');
require_once(__DIR__ . '/services/ActivitiEngineService.php');
require_once(__DIR__ . '/services/ActivitiRuntimeService.php');
require_once(__DIR__ . '/services/ActivitiJobsService.php');
require_once(__DIR__ . '/services/ActivitiUsersService.php');
require_once(__DIR__ . '/services/ActivitiGroupsService.php');


class ActivitiClient extends ActivitiClientBase
{

	/**
	 * @var ActivitiDeploymentService
	 */
	public $deployment;
	
	/**
	 * @var ActivitiProcessDefinitionsService
	 */
	public $processDefinitions;
	
	/**
	 * @var ActivitiModelsService
	 */
	public $models;
	
	/**
	 * @var ActivitiProcessInstancesService
	 */
	public $processInstances;
	
	/**
	 * @var ActivitiExecutionsService
	 */
	public $executions;
	
	/**
	 * @var ActivitiTasksService
	 */
	public $tasks;
	
	/**
	 * @var ActivitiHistoryService
	 */
	public $history;
	
	/**
	 * @var ActivitiFormsService
	 */
	public $forms;
	
	/**
	 * @var ActivitiDatabaseTablesService
	 */
	public $databaseTables;
	
	/**
	 * @var ActivitiEngineService
	 */
	public $engine;
	
	/**
	 * @var ActivitiRuntimeService
	 */
	public $runtime;
	
	/**
	 * @var ActivitiJobsService
	 */
	public $jobs;
	
	/**
	 * @var ActivitiUsersService
	 */
	public $users;
	
	/**
	 * @var ActivitiGroupsService
	 */
	public $groups;
	
	
	/**
	 * Initialize sub services
	 */
	public function __construct()
	{
		$this->deployment = new ActivitiDeploymentService($this);
		$this->processDefinitions = new ActivitiProcessDefinitionsService($this);
		$this->models = new ActivitiModelsService($this);
		$this->processInstances = new ActivitiProcessInstancesService($this);
		$this->executions = new ActivitiExecutionsService($this);
		$this->tasks = new ActivitiTasksService($this);
		$this->history = new ActivitiHistoryService($this);
		$this->forms = new ActivitiFormsService($this);
		$this->databaseTables = new ActivitiDatabaseTablesService($this);
		$this->engine = new ActivitiEngineService($this);
		$this->runtime = new ActivitiRuntimeService($this);
		$this->jobs = new ActivitiJobsService($this);
		$this->users = new ActivitiUsersService($this);
		$this->groups = new ActivitiGroupsService($this);
	}
	
}

