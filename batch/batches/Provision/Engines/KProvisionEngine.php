<?php
/**
 * base class for the real ProvisionEngine in the system - currently only akamai 
 * 
 * @package Scheduler
 * @subpackage Provision
 * @abstract
 */
abstract class KProvisionEngine
{
	/**
	 * @var KSchedularTaskConfig
	 */
	protected $taskConfig = null;
	
	
	/**
	 * Will return the proper engine depending on the type (KalturaConversionEngineType)
	 *
	 * @param int $provider
	 * @param KSchedularTaskConfig $taskConfig
	 * @return KProvisionEngine
	 */
	public static function getInstance ( $provider , KSchedularTaskConfig $taskConfig )
	{
		$engine =  null;
		
		switch ($provider )
		{
			case KalturaSourceType::AKAMAI_LIVE:
				$engine = new KProvisionEngineAkamai( $taskConfig );
				break;
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
	 * @param KalturaBatchJob $job
	 * @param KalturaProvisionJobData $data
	 * @return KProvisionEngineResult
	 */
	abstract public function provide( KalturaBatchJob $job, KalturaProvisionJobData $data );
	
	/**
	 * @param KalturaBatchJob $job
	 * @param KalturaProvisionJobData $data
	 * @return KProvisionEngineResult
	 */
	abstract public function delete( KalturaBatchJob $job, KalturaProvisionJobData $data );
}


/**
 * @package Scheduler
 * @subpackage Conversion
 *
 */
class KProvisionEngineResult
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
	public function __construct( $status , $errMessage, KalturaProvisionJobData $data = null )
	{
		$this->status = $status;
		$this->errMessage = $errMessage;
		$this->data = $data;
	}
}

