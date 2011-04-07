<?php
/**
 * base class for the real KBulkUploadEngine in the system 
 * 
 * @package Scheduler
 * @subpackage BulkUpload
 * @abstract
 */
abstract class KBulkUploadEngine
{
	/**
	 * @var KSchedularTaskConfig
	 */
	protected $taskConfig = null;
		
	/**
	 * Will return the proper engine depending on the type (KalturaBulkUploadTypeType)
	 *
	 * @param int $provider
	 * @param KSchedularTaskConfig $taskConfig
	 * @return KBulkUploadEngine
	 */
	public static function getInstance ( $provider , KSchedularTaskConfig $taskConfig )
	{
		$engine =  null;
		
		switch ($provider )
		{
			case KalturaBulkUploadType::CSV:
				$engine = new KBulkUploadEngineCsv($taskConfig);
				break;
			case KalturaBulkUploadType::XML:
				$engine = new KBulkUploadEngineXml($taskConfig);
				break;
			default:
				$engine = KalturaPluginManager::loadObject('KBulkUploadEngine', $provider, array($taskConfig));
		}
				
		return $engine;
	}

	/**
	 * @param KSchedularTaskConfig $taskConfig
	 */
	protected function __construct( KSchedularTaskConfig $taskConfig )
	{
		$this->taskConfig = $taskConfig;
	}
	
	/**
	 * @return string
	 */
	abstract public function getName();
	
	/**
	 * 
	 * @param KalturaBatchJob $job
	 * @param KalturaBulkUploadJobData $data
	 * @return KBulkUploadEningeResult
	 */
	protected public function handleBulkUpload( KalturaBatchJob $job, KalturaBulkUploadJobData $data )
	{
		//TODO: Roni create the flow:
		//0. Init - maybe even give client :)
		//1. Validate - even if empty
		//2. Parse rows
		//3. Close
		
		$this->init($job, $data);
		$this->validateFile($job, $data);
		$this->parseRows($job, $data);
		$this->close($job, $data);
	}

	/**
	 * 
	 * Validates the given file for the job
	 * @param KalturaBatchJob $job
	 * @param KalturaBulkUploadJobData $data
	 */
	protected function validateFile(KalturaBatchJob $job, KalturaBulkUploadJobData $data );
	
	/**
	 * 
	 * Parse the rows of the given bulk job file and perform the needed actions
	 * @param KalturaBatchJob $job
	 * @param KalturaBulkUploadJobData $data
	 */
	protected function parseRows(KalturaBatchJob $job, KalturaBulkUploadJobData $data );
	
	/**
	 * 
	 * Inits the engine with the needed params of the batch job 
	 * @param KalturaBatchJob $job
	 * @param KalturaBulkUploadJobData $data
	 */
	protected function init(KalturaBatchJob $job, KalturaBulkUploadJobData $data );
		
	/**
	 * 
	 * closes the engine with the needed params of the batch job 
	 * @param KalturaBatchJob $job
	 * @param KalturaBulkUploadJobData $data
	 */
	protected function close(KalturaBatchJob $job, KalturaBulkUploadJobData $data );
}

/**
 * @package Scheduler
 * @subpackage Conversion
 *
 */
class KBulkUploadEngineResult
{
	/**
	 * @var int
	 */
	public $status;
	
	/**
	 * @var string
	 */
	public $errMessage;
	
	/**
	 * @var KalturaProvisionJobData
	 */
	public $data;
	
	/**
	 * @param int $status
	 * @param string $errMessage
	 * @param KalturaProvisionJobData $data
	 */
	public function __construct( $status , $errMessage, KalturaBulkUploadJobData $data = null )
	{
		$this->status = $status;
		$this->errMessage = $errMessage;
		$this->data = $data;
	}
}

