<?php

class KBatchKillerConfig
{
	/**
	 * @var int the batch process pid
	 */
	public $pid;
	
	/**
	 * @var int max idle time in seconds
	 */
	public $maxIdleTime;
	
	/**
	 * @var int sleep time in soconds
	 */
	public $sleepTime;
	
	/**
	 * @var array file paths to check
	 */
	public $files;
	
	/**
	 * @var string batch unique key
	 */
	public $sessionKey;
	
	/**
	 * @var int batch instance index
	 */
	public $batchIndex;
	
	/**
	 * @var string batch name
	 */
	public $batchName;
	
	/**
	 * @var string worker id
	 */
	public $workerId;
	
	/**
	 * @var string worker type
	 */
	public $workerType;
	
	/**
	 * @var string scheduler id
	 */
	public $schedulerId;
	
	/**
	 * @var string scheduler name
	 */
	public $schedulerName;
	
	/**
	 * @var string path to DWH log
	 */
	public $dwhPath;
}