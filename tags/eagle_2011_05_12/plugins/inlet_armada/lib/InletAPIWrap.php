<?php
#include_once "XmlRpcWrap.php";

class InletArmadaJobStatus{

	const Created 		= 0;
	const QueuedNew		= 0x10;
	const QueuedRetry	= 0x11;
	const ProcessingActive = 0x20;
	const ProcessingPaused = 0x21;
	const CompletedUnknown = 0x40;
	const CompletedSuccess = 0x41;
	const CompletedFailure = 0x42;
};

class InletAPIWrap {

protected 
		$serverUrl=null,
		$sessionId=null;

	/* ----------------------
	 * Cont/Dtor
	 */
	public function __construct($serverUrl) {
		$this->serverUrl=$serverUrl;
	}
	public function __destruct() {
		unset($this);
	}
	
	/***********************
	 *	
	 */
	public function userLogon($login, $passw, &$rvObj=null)
	{
		$rv=xmlrpc_call_method($this->serverUrl,__FUNCTION__, 
			array($login, $passw));
		$this->sessionId=$rv->session_id;
		
		if($rvObj) $rvObj=$rv;
		if($rv && $rv->response && $rv->response=="ok")
			return true;
		if($rvObj){
			$rv->url=$this->serverUrl;
			$rv->login=$login;
			$rv->passw=$passw;
		}
		return false;
		
	}
	
	/***********************
	 *	
	 */
	public function userLogoff(&$rvObj=null)
	{
		$rv=xmlrpc_call_method($this->serverUrl,__FUNCTION__, 
			array($this->sessionId));
		
		if($rvObj) $rvObj=$rv;
		if($rv && $rv->response && $rv->response=="ok")
			return true;
		return false;
	}
	
	/***********************
	 *
	 */
	public function templateGroupList(&$rvObj)
	{
		$rv=xmlrpc_call_method($this->serverUrl,__FUNCTION__, 
			array($this->sessionId));
		
		if($rvObj) $rvObj=$rv;
		if($rv && $rv->response && $rv->response=="ok")
			return true;
		return false;
	}
	
	/***********************
	 *
	 */
	public function jobAdd($jobTemplateId, $jobSourceFile, $jobDestFile, $priority, $desc, $jobCustomerIdList=array(), $nodeGuid="", &$rvObj=null)
	{

		$rv=xmlrpc_call_method($this->serverUrl, __FUNCTION__, 
			array(
				$this->sessionId,
				array($jobTemplateId,"int"),			// job template id
				$jobSourceFile, //'c:\tmp\try1.mp4',		// String job_source_file, 
				$jobDestFile,	// 'f:\output\zzz.mp4',		// String job_destination_file, 
				array( $priority, "int"),				// Int priority, 
				$desc,						// String description, 
//			array(array(),"array"),							// List job_customer_id_list, 
//			""							// String node_guid (optional)
				array($jobCustomerIdList,"array"),							// List job_customer_id_list, 
				$nodeGuid							// String node_guid (optional)
			));

		if($rvObj) $rvObj=$rv;
		if($rv && $rv->response && $rv->response=="ok")
			return true;
		if($rvObj){
			$rv->jobTemplateId=$jobTemplateId;
			$rv->jobSourceFile=$jobSourceFile;
			$rv->jobDestFile=$jobDestFile;
		}
		return false;
	}

	/***********************
	 *
	 */
	public function jobDelete($jobId, &$rvObj=null)
	{
		$rv=xmlrpc_call_method($this->serverUrl,__FUNCTION__, 
			array($this->sessionId,
				array($jobId,"int")));
		
		if($rvObj) $rvObj=$rv;
		if($rv && $rv->response && $rv->response=="ok")
			return true;
		if($rvObj){
			$rv->jobId=$jobId;
		}
		return false;
	}

	/***********************
	 *
	 */
	public function jobListActive(&$rvObj)
	{
		$rv=xmlrpc_call_method($this->serverUrl,__FUNCTION__, 
			array($this->sessionId));
		
		if($rvObj) $rvObj=$rv;
		if($rv && $rv->response && $rv->response=="ok")
			return true;
		return false;
	}

	/***********************
	 *
	 */
	public function jobListCompleted($maxJobs, $jobsSince, &$rvObj)
	{;
		$rv=xmlrpc_call_method($this->serverUrl,__FUNCTION__, 
			array($this->sessionId,
				array($maxJobs,"int"),
				array($jobsSince, "date")));

		if($rvObj) $rvObj=$rv;
		if($rv && $rv->response && $rv->response=="ok")
			return true;
		return false;
	}

	/***********************
	 *
	 */
	public function jobList(array $jobIds, &$rvObj)
	{
	$ids = array();
		foreach($jobIds as $id) {
			$ids[] = array($id,"int");
		}
		$rv=xmlrpc_call_method($this->serverUrl,__FUNCTION__, 
			array($this->sessionId,
				array($ids,"array")));
		
		if($rvObj) $rvObj=$rv;
		if($rv && $rv->response && $rv->response=="ok")
			return true;
		return false;
	}

}

