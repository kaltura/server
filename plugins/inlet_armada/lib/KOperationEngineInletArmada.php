<?php
/**
 * 
 * @package Scheduler
 * @subpackage Conversion
 *
 */
class KOperationEngineInletArmada  extends KSingleOutputOperationEngine
{

	protected $url=null;
	protected $login=null;
	protected $passw=null;
	protected $prio=5;

	public function __construct($cmd, $outFilePath)
	{
		parent::__construct($cmd,$outFilePath);
//		$this->prio=5;
		KalturaLog::info(": cmd($cmd), outFilePath($outFilePath)");
	}

	/*************************************
	 * 
	 */
	protected function getCmdLine()
	{
		$exeCmd =  parent::getCmdLine();
		KalturaLog::info(print_r($this,true));
		return $exeCmd;
	}

	/*************************************
	 * 
	 */
	public function operate(kOperator $operator = null, $inFilePath, $configFilePath = null)
	{
//$this->outFilePath = "k:".$this->outFilePath;
		KalturaLog::debug("operator===>".print_r($operator,1));
/*		if(mkdir($this->outFilePath))
			KalturaLog::debug("SUCCESS");
		else 
			KalturaLog::debug("FAILURE");
*/
$encodingTemplate;
		sscanf($operator->extra,"encodingTemplate=%s",&$encodingTemplate);

		$inlet = new InletAPIWrap($this->url);
		KalturaLog::debug(print_r($inlet,1));
		$rvObj=new XmlRpcData;
		
		$rv=$inlet->userLogon($this->login, $this->passw, $rvObj);
		if(!$rv) {
			throw new KOperationEngineException("Inlet failure: login, rv(".(print_r($rvObj,true)).")");
		}
		KalturaLog::debug("userLogon - ".print_r($rvObj,1));
		
		$rv=$inlet->jobAdd(			
				$encodingTemplate,			// job template id
				$inFilePath,		// String job_source_file, 
				$this->outFilePath,		// String job_destination_file, 
				$this->prio,				// Int priority, 
				$inFilePath,			// String description, 
				array(),"",
				$rvObj);						
		if(!$rv) {
			throw new KOperationEngineException("Inlet failure: add job, rv(".print_r($rvObj,1).")");
		}
		KalturaLog::debug("jobAdd - encodingTemplate($encodingTemplate), inFilePath($inFilePath), outFilePath($this->outFilePath),rv-".print_r($rvObj,1));
		
		$jobId=$rvObj->job_id;
		$attemptCnt=0;
		while ($jobId) {
			sleep(60);
			$rv=$inlet->jobList(array($jobId),$rvObj);
			if(!$rv) {
				throw new KOperationEngineException("Inlet failure: job list, rv(".print_r($rvObj,1).")");
			}
			switch($rvObj->job_list[0]->job_state){
			case InletArmadaJobStatus::CompletedSuccess:
				$jobId=null;
				break;
			case InletArmadaJobStatus::CompletedUnknown:
			case InletArmadaJobStatus::CompletedFailure:
				throw new KOperationEngineException("Inlet failure: job, rv(".print_r($rvObj,1).")");
				break;
			}
			if($attemptCnt%10==0) {
				KalturaLog::debug("waiting for job completion - ".print_r($rvObj,1));
			}
			$attemptCnt++;
		}
		
		KalturaLog::debug("Job completed successfully - ".print_r($rvObj,1));
		copy($inFilePath, $this->outFilePath);
/*
		parent::operate($operator, $inFilePath, $configFilePath);
		rename("$this->outFilePath//playlist.m3u8", "$this->outFilePath//playlist.tmp");
		self::parsePlayList("$this->outFilePath//playlist.tmp","$this->outFilePath//playlist.m3u8");
*/
	}

	/*************************************
	 * 
	 */
	public function configure(KSchedularTaskConfig $taskConfig, KalturaConvartableJobData $data)
	{
		parent::configure($taskConfig, $data);

		$errStr=null;
		if(!$taskConfig->params->InletArmadaUrl)
			$errStr="InletArmadaUrl";
		if(!$taskConfig->params->InletArmadaLogin){
			if($errStr) 
				$errStr.=",InletArmadaLogin";
			else
				$errStr="InletArmadaLogin";
		}
		if(!$taskConfig->params->InletArmadaPassword){
			if($errStr) 
				$errStr.=",InletArmadaPassword";
			else
				$errStr="InletArmadaPassword";
		}
		
		if($errStr)
			throw new KOperationEngineException("Inlet failure: missing credentials - $errStr");//, url(".$taskConfig->params->InletArmadaUrl."), login(."$taskConfig->params->InletArmadaLogin."),passw(".$taskConfig->params->InletArmadaPassword.")");
		
		$this->url =	$taskConfig->params->InletArmadaUrl;
		$this->login =	$taskConfig->params->InletArmadaLogin;
		$this->passw =	$taskConfig->params->InletArmadaPassword;
		if($taskConfig->params->InletArmadaPriority)
			$this->prio =	$taskConfig->params->InletArmadaPriority;
		else
			$this->prio = 5;
		KalturaLog::info("taskConfig-->".print_r($taskConfig,true)."\ndata->".print_r($data,true));
	}

	/*************************************
	 * 
	 */
	private function parsePlayList($fileIn, $fileOut)
	{
		$fdIn = fopen($fileIn, 'r');
		if($fdIn==false)
			return false;
		$fdOut = fopen($fileOut, 'w');
		if($fdOut==false)
			return false;
		$strIn=null;
		while ($strIn=fgets($fdIn)){
			if(strstr($strIn,"---")){
				$i=strrpos($strIn,"/");
				$strIn = substr($strIn,$i+1);
			}
			fputs($fdOut,$strIn);
			echo $strIn;
		}
		fclose($fdOut);
		fclose($fdIn);
		return true;
	}
}
