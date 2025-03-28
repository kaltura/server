<?php
 
/*****************************
 * Includes & Globals
 */
ini_set("memory_limit","512M");

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	/****************************
	 * KChunkedEncodeMemcacheWrap
	 */
	class KChunkedEncodeMemcacheWrap implements KChunkedEncodeDistrExecInterface
	{
		const WRITE_IDX = "ChunkedEncodeWriteIdx";
		const READ_IDX  = "ChunkedEncodeReadIdx";
		
		const JOB_KEYNAME_PREFIX = "ChunkedEncodeJob";
		const SEMAPHORE_PREFIX = "ChunkedEncodeSemaphore";
		
		protected $cacheStore = null;
		protected $expiry = 86400;
		protected $memcacheConfig = null;
		
		protected $writeIndex = null;
		protected $readIndex = null;
				
		protected $storeToken = null;
		
		/* ---------------------------
		 * C'tor
		 */
		public function __construct($storeToken=null, $host=null, $port=null, $flags=1)
		{
			$this->storeToken = $storeToken;
				// 'flags=1' stands for 'compress stored data'
			if(isset($host) && isset($port) && isset($flags)){
				$this->Setup(array('host'=>$host, 'port'=>$port, 'flags'=>$flags));
			}
		}

		/* ---------------------------
		 * Setup
		 */
		public function Setup($config)
		{
			$this->cacheStore = new kInfraMemcacheCacheWrapper();
			if($this->cacheStore->init($config)===false){
				KalturaLog::log("cacheStore failed to initialize with config:".serialize($config));
				false;
			}
			$this->memcacheConfig = $config;
			return $this->initialize();
		}
		
		/* ---------------------------
		 * Setup
		 */
		public function SetupWithCacheType($cacheType)
		{
			if(!isset($cacheType))
				$cacheType = kCacheManager::CACHE_TYPE_LIVE_MEDIA_SERVER . '_0';
				/*
				 * Create the memcacah store object
				 */
			$this->cacheStore = kCacheManager::getSingleLayerCache($cacheType);
			if(!$this->cacheStore) {
				KalturaLog::log("cacheStore is null. cacheType: $cacheType . returning false");
				return false;
			}
			
			return $this->initialize();
		}
		
		/* ---------------------------
		 * SaveJob
		 *	Store job to memcache storage
		 */
		public function SaveJob($job)
		{
			$key = $this->getJobKeyName($job->keyIdx);
			$str = serialize($job);
			if($this->set($key, $str, $this->expiry)===false){
				KalturaLog::log("Session($job->session) - Failed to set job $key($str)");
				return false;
			}
				// Just to remove non printables from the log msg
			$str = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $str);
			KalturaLog::log("Job:$str");
			KalturaLog::log("Session($job->session) - Set job key:$key, state:$job->state");
			return true;
		}
		 
		/* ---------------------------
		 * AddJob
		 */
		public function AddJob($job)
		{
			$job->keyIdx = $this->allocateJobIndex();
			return $this->SaveJob($job);
		}
		
		/* ---------------------------
		 * FetchJob
		 *	Get job from memcache storage
		 */
		public function FetchJob($keyIdx, $maxTries=10)
		{
			$key = $this->getJobKeyName($keyIdx);
			/*
			 * 10 attempts to get the job object
			 */
			for($try=0; $try<$maxTries; $try++) {
				if(($jobStr=$this->get($key))!==false){
					break;
				}
				usleep(rand(0,30000));
				KalturaLog::log("Attempt($try) to fetch job ($keyIdx)");
			}
			if($try==$maxTries){
				KalturaLog::log("Job($keyIdx) - Failed to get job ($keyIdx)");
				return false;
			}
			$job = unserialize($jobStr);
			return $job;
		}
		 
		/* ---------------------------
		 * DeleteJob
		 */
		public function DeleteJob($keyIdx)
		{
			$key = $this->getJobKeyName($keyIdx);
			$this->delete($key);
			KalturaLog::log("Job($keyIdx) - Deleted");
			return true;
		}
		
		/* ---------------------------
		 * GetActiveSessions
		 *	
		 */
		public function GetActiveSessions()
		{
			if($this->fetchReadWriteIndexes($writeIndex, $readIndex)===false){
				KalturaLog::log("ERROR: Missing write or read index ");
				return false;
			}
				/*
				 * Stop fetching attempts if there are no unread objects
				 */
			if($readIndex>=$writeIndex+1) 
				return array();;

			KalturaLog::log("RD:$readIndex, WR:$writeIndex");
			$sessions = array();
			for($idx=$readIndex; $idx<$writeIndex; $idx++){
				$job = $this->FetchJob($idx);
				if($job===false){
					continue;
				}
				if(array_key_exists($job->session, $sessions)==true)
					$sessions[$job->session] = $sessions[$job->session]+1;
				else
					$sessions[$job->session] = 1;
			}
			return $sessions;
		}
		
		/* ---------------------------
		 * initilize
		 */
		protected function initialize()
		{
				/*
				 * Get the global /read/write indexes,
				 * if don't exist - create them w/out expiry time
				 */
			$this->fetchReadWriteIndexes($writeIndex, $readIndex);
			$writeIndexKeyName = $this->getWriteIndexKeyName();
			if(!isset($writeIndex)){
				$writeIndex = 0;
				if($this->set($writeIndexKeyName,$writeIndex,0)===false) {
					KalturaLog::log("Failed to create WR index ($writeIndexKeyName)");
					return false;
				}
			}
			$this->writeIndex = $writeIndex;
			KalturaLog::log("Current WR index value ($writeIndexKeyName:$writeIndex)");
			
			$readIndexKeyName = $this->getReadIndexKeyName();
			if(!isset($readIndex)){
				$readIndex = 1;
				if($this->set($readIndexKeyName,$readIndex,0)===false) {
					KalturaLog::log("Failed to create RD index ($readIndexKeyName)");
					return false;
				}
			}
			$this->readIndex = $readIndex;
			KalturaLog::log("Current RD index value ($readIndexKeyName:$readIndex)");
			return true;
		}
		
		/* ---------------------------
		 *
		 
		public function incrementReadIndex()
		{
			return $this->increment($this->getReadIndexKeyName());
		}
*/
		/* ---------------------------
		 * fetchReadWriteIndexes
		 *	
		 */
		public function fetchReadWriteIndexes(&$writeIndex, &$readIndex)
		{
			$writeIndexKeyName = $this->getWriteIndexKeyName();
			if(($writeIndex=$this->get($writeIndexKeyName))===false){
				KalturaLog::log("Missing WR index ($writeIndexKeyName)");
				$writeIndex = null;
			}
			else
				KalturaLog::log("Current WR index value ($writeIndexKeyName:$writeIndex)");

			$readIndexKeyName = $this->getReadIndexKeyName();		
			if(($readIndex=$this->get($readIndexKeyName))===false){
				KalturaLog::log("Missing RD index ($readIndexKeyName)");
				$readIndex = null;
			}
			else
				KalturaLog::log("Current RD index value ($readIndexKeyName: $readIndex)");
			if(isset($readIndex) && isset($writeIndex))
				return true;
			else
				return false;
		}
		
		/* ---------------------------
		 * getReadIndexKeyName
		 *	Compose global read-index
		 */
		protected function getReadIndexKeyName()
		{
			return self::READ_IDX.$this->storeToken;
		}

		/* ---------------------------
		 * getWriteIndexKeyName
		 * 	Compose global write-index
		 */
		protected function getWriteIndexKeyName()
		{
			return self::WRITE_IDX.$this->storeToken;
		}

		/* ---------------------------
		 * getJobKeyName
		 *	Compose job key name, 
		 *	using manager::JOB_KEYNAME_PREFIX const and 'storeToken'
		 */
		protected function getJobKeyName($keyIdx)
		{
			return self::JOB_KEYNAME_PREFIX.$this->storeToken."_$keyIdx";
		}

		/* ---------------------------
		 * getSemaphoreKeyName
		 *	Compose job semaphore key name, 
		 * 	using manager::SEMAPHORE_PREFIX const and 'storeToken'
		 */
		protected function getSemaphoreKeyName($keyIdx)
		{
			return self::SEMAPHORE_PREFIX.$this->storeToken."_$keyIdx";
		}
		
		/* ---------------------------
		 * allocateJobIndex
		 *	Allocate an index for a new job
		 */
		protected function allocateJobIndex()
		{
			return $this->increment($this->getWriteIndexKeyName());
		}

		/* ---------------------------
		 * lock
		 *	based on memcache 'add'  functionality  
		 */
		protected function lock($key, $val, $attempDuration=60, $attemptSleep=0.005, $expiry=3600) // expiry = 1hr
		{
			$waited=0;
	KalturaLog::log("key($key), val($val)");
			$attemptSleep*=1000000;
			do {
				$addLasted = microtime(true);
				$rv = $this->cacheStore->add($key, $val, $expiry);
				$addLasted = round((microtime(true)-$addLasted),3);
				if($rv===true){
					break;
				}
				$sleepLasted = microtime(true);
				usleep(rand(0,$attemptSleep));
				$sleepLasted = round((microtime(true)-$sleepLasted),5);
				$waited+=($addLasted+$sleepLasted);
				$attempDuration-= ($addLasted+$sleepLasted);
	KalturaLog::log("attempDuration($attempDuration)");
			}
			while($attempDuration>0);
			if($waited>0){
	KalturaLog::log("EXIT:key($key),val($val): rv($rv),waited($waited)");
			}
			else {
	KalturaLog::log("EXIT:key($key),val($val): rv($rv)");
			}
			return $rv;
		}

		/* ---------------------------
		 * delete
		 */
		protected function delete($key)
		{
			return $this->cacheStore->delete($key);
		}
		
		/* ---------------------------
		 * add
		 *	memcache low level 
		 */
		protected function add($key, $val, $expiry=86400)
		{
			return $this->cacheStore->add($key, $val, $expiry);
		}
		
		/* ---------------------------
		 * set
		 *	memcache low level 
		 */
		protected function set($key, $val, $expiry=86400)
		{
			return $this->cacheStore->set($key, $val, $expiry);
		}
		
		/* ---------------------------
		 * get
		 *	memcache low level 
		 */
		protected function get($key)
		{
			return $this->cacheStore->get($key);
		}
		
		/* ---------------------------
		 * increment
		 *	memcache low level 
		 */
		protected function increment($key, $value=1)
		{
			return $this->cacheStore->increment($key, $value);
		}
		
		/* ---------------------------
		 * decrement
		 *	memcache low level 
		 */
		protected function decrement($key, $value=1)
		{
			return $this->cacheStore->decrement($key, $value);
		}
		
		/* ---------------------------
		 * ExecuteSession
		 */
		public static function ExecuteSession($host, $port, $token, $concurrent, $concurrentMin, $sessionName, $cmdLine, $sharedChunkPath = null, $ffmpegBin=null, $ffprobeBin=null)
		{
			KalturaLog::log("host:$host, port:$port, token:$token, concurrent:$concurrent, concurrentMin:$concurrentMin, sessionName:$sessionName, cmdLine:$cmdLine, sharedChunkPath:$sharedChunkPath");
			$storeManager = new KChunkedEncodeMemcacheWrap($token);
				// 'flags=1' stands for 'compress stored data'
			$config = array('host'=>$host, 'port'=>$port, 'flags'=>1);
			$storeManager->Setup($config);
			
			$setup = new KChunkedEncodeSetup();
			$setup->concurrent = $concurrent;
			$setup->concurrentMin = $concurrentMin;
			$setup->cleanUp = 0;
			$setup->cmd = $cmdLine;
			$setup->sharedChunkPath = $sharedChunkPath;
			if(isset($ffmpegBin))
				$setup->ffmpegBin = $ffmpegBin;
			if(isset($ffprobeBin))
				$setup->ffprobeBin = $ffprobeBin;
/* ========================
   ========================
   FFmpeg6 Intergration
   Following code is part of the FFMpeg6 intgeration procudere.
   it should be removed upon FFMpeg6 approval 
   ======================== */
			if(isset($ffprobeBin)) {
KalturaLog::log("ffprobeBin:$ffprobeBin");
				list($part, $ffprobeBin) = 
					KFFmpegToPartnerMatch::extractPartnerId($ffprobeBin);
				$setup->ffprobeBin = $ffprobeBin;
				KFFmpegToPartnerMatch::match($part);
KalturaLog::log("patrner:$part, ffprobeBin:$ffprobeBin");
			}
			if(KFFmpegToPartnerMatch::getVersion()==4)
				$chunker = new KChunkedEncode4($setup);
			else
				$chunker = new KChunkedEncode($setup);
/* ======================== */

			$session = new KChunkedEncodeSessionManager($setup, $storeManager, $sessionName, $chunker);
			
			if(($rv=$session->Initialize())!=true) {
				$session->Report();
				return $rv;
			}
			$rv = $session->Generate();
			$session->Report();
			return array($rv, $session);
		}

	}
	/*****************************
	 * End of KChunkedEncodeMemcacheWrap
	 *****************************/
	
	/****************************
	 * KChunkedEncodeMemcacheScheduler
	 */
	class KChunkedEncodeMemcacheScheduler extends KChunkedEncodeMemcacheWrap implements KChunkedEncodeDistrSchedInterface
	{
		protected $tmpFolder = null;
		
		/* ---------------------------
		 * C'tor
		 */
		public function __construct($storeToken=null, $tmpFolder=null)
		{
			parent::__construct($storeToken);
			$this->tmpFolder = $tmpFolder;
		}
		
		/* ---------------------------
		 * FetchNextJob
		 *	Get next job from the mmecache storage
		 * 	if missing => return false 
		 * fetchRangeRandMax - represents the max randomization range of the fetched job index (between the readIdx and writeIdx).
		 * Motivation - to ease race condition over the locking of the fetch/read idx, 
		 * when large number of srvs attempt to fetch the same job. 
		 * This situation occurred in AWS env, with high connect/'walk around' times to DC memcache srv
		 */
		public function FetchNextJob($fetchRangeRandMax=0){
			if($fetchRangeRandMax==0)
				return $this->FetchNextJobIndexInc($fetchRangeRandMax);
			else
				return $this->FetchNextJobRandom($fetchRangeRandMax);
		}
		public function FetchNextJobRandom($fetchRangeRandMax=50)
		{
			$writeIndex = null;
			$readIndex = null;
			
			// semaphore token - process-id + hostname + rand
			$semaphoreToken = getmypid().".".gethostname().".".rand();
			while(true) {
				if($this->fetchReadWriteIndexes($writeIndex, $readIndex)===false){
					KalturaLog::log("ERROR: Missing write or read index ");
					return false;
				}
					/*
					 * Stop fetching if there are no unread objects
					 */
				if($readIndex>=$writeIndex+1) 
					break;
				
					/*
					 * Evaluate the fetch job index, within the 'fetchRangeRandMax' value.
					 * If the chunk Q is smaller than the fetch range - take the job in the readIdx
					 */
				$queueSize = $writeIndex-$readIndex;
				if($fetchRangeRandMax==0 || $queueSize<$fetchRangeRandMax)
					$fetchIndex = $readIndex;
				else
					$fetchIndex = rand($readIndex, min($readIndex+$fetchRangeRandMax, $writeIndex));
                			KalturaLog::log("RD:$readIndex, WR:$writeIndex, FCH:$fetchIndex");

					/*
					 * Try to lock the next unread job object
					 * if failed - carry on to next
					 */
				$semaphoreKey = $this->getSemaphoreKeyName($fetchIndex);
				
				$rv = $this->lock($semaphoreKey, $semaphoreToken, 0);
				if($rv!==true){
					if($fetchIndex==$readIndex && $this->setReadIndex($readIndex+1)===false)
						return false;
					KalturaLog::log("Unable to lock fetchIndex($fetchIndex), skipping to the next ");
					continue;
				}
				
					/*
					 * Try to fetch the job object from the memcache storage
					 * If failed - delete the sempahore (unlock) and try the next one
					 */
				$job = $this->FetchJob($fetchIndex); 
				if($job!==false) {
					return $job;
				}
				else {
					$this->delete($semaphoreKey);
					KalturaLog::log("Unable to access job ($fetchIndex), skip to next");
					if($fetchIndex==$readIndex && $this->setReadIndex($readIndex+1)===false)
						return false;
				}
			}
			
			return null;
		}
		
		public function FetchNextJobIndexInc($fetchRangeRandMax=50){
			if($this->fetchReadWriteIndexes($writeIndex, $readIndex)===false){
				KalturaLog::log("ERROR: Missing write or read index ");
				return false;
			}
				/*
				 * Stop fetching if there are no unread objects
				 */
			if($readIndex>$writeIndex){
				return null;
			}
			
			$fetchIndex = $this->incrementReadIndex()-1;
			KalturaLog::log("fetchIndex:$fetchIndex (new)");

			$semaphoreKey = $this->getSemaphoreKeyName($fetchIndex);
			$semaphoreToken = getmypid().".".gethostname().".".rand();
			$rvLock = null;
			$attempts=10;
			while(true) {
						/*
						 * Try to lock the next unread job object
						 * if failed - carry on to next
						 */
				if(is_null($rvLock)) $rvLock = $this->lock($semaphoreKey, $semaphoreToken, 0);
				if($rvLock!==true){
					KalturaLog::log("Unable to lock fetchIndex($fetchIndex), retry");
				}
				else {
						/*
						 * Try to fetch the job object from the memcache
						 */
					$job = $this->FetchJob($fetchIndex); 
					if($job!==false) {
						return $job;
					}
				}

				if($this->fetchReadWriteIndexes($writeIndex, $readIndex)===false){
					KalturaLog::log("ERROR: Missing write or read index ");
					break;
				}
				
				if($fetchIndex<$readIndex-1000 || $attempts==0) {
					KalturaLog::log("Unable to access job($fetchIndex), while rdIdx($readIndex) moved on, skipping");
					break;
				}

				$attempts--;
				KalturaLog::log("Unable to access job ($fetchIndex), retry ($attempts)");
				usleep(rand(0,100000));
			}
			
			$this->delete($semaphoreKey);
			return false;
		}

		/* ---------------------------
		 * GetRunningJobs
		 */
		public function GetRunningJobs($lookBackward=500) {
			KalturaLog::log("lookBackward:$lookBackward");
			if($this->fetchReadWriteIndexes($writeIndex, $readIndex)===false){
				KalturaLog::log("ERROR: Missing write or read index ");
				return false;
			}
			$localHostname = gethostname();
			$jobs = array();
			$idx=max(0,$readIndex-$lookBackward);
			$idxMax = min($readIndex+$lookBackward,$writeIndex);
			for(; $idx<$idxMax; $idx++) {
				$job = $this->FetchJob($idx,1);
				if($job!==false && $job->state==$job::STATE_RUNNING 
				&& strcmp($localHostname, $job->hostname)=== 0 && KProcessExecutionData::isProcessRunning($job->process)) {
					$jobs[] = $job;
					KalturaLog::log("Job: keyIdx($idx), pId($job->process)");
				}
			}
			KalturaLog::log("Found running jobs - ".count($jobs));
			return($jobs);
		}
		
		/* ---------------------------
		 * RefreshJobs
		 */
		public function RefreshJobs($maxSlots, $fetchRangeRandMax, &$jobs)
		{
				/*
				 * Get list of per scheduler running jobs
				 */
			$refreshed = $this->refetchJobs($jobs);
			$running = count($jobs);
			if($running>0 && $refreshed==0){
				return false;
			}
				/*
				 * If there are no free execution slots - wait and retry
				 */
			if($running>=$maxSlots) {
				KalturaLog::log("Running:$running - No free job slots, maxSlots:$maxSlots");
				return null;
			}

				/*
				 * If there are no pending jobs - wait and retry
				 */
			$job = $this->FetchNextJob($fetchRangeRandMax);
			if($job===null){
				KalturaLog::log("Running:$running - No pending jobs");
				return null;
			}
			else if($job===false){
				KalturaLog::log("Failed to fetch next job");
				return false;
			}

			if($this->ExecuteJob($job)==true)
				$jobs[$job->keyIdx] = $job;
			
			return true;
		}

		/* ---------------------------
		 * refetchJobs
		 * 	Reload the givven jobs array from memcache storage
		 * 	Remove finished or retried jobs 
		 */
		protected function refetchJobs(&$jobs)
		{
			$cnt=0;
			foreach($jobs as $idx=>$job) {
				$job = $this->FetchJob($job->keyIdx);
				if($job===false) {
					KalturaLog::log("Missing $idx");
					continue;
				}
				if($job->isFinished() || $job->isRetry()) {
					unset($jobs[$idx]);
				}
				else {
					$jobs[$idx] = $job;
				}
				$cnt++;
			}
			return $cnt;
		}

		/* ---------------------------
		 * getPhpChunkJobLogName
		 */
		protected static function getPhpChunkJobLogName($job)
		{
			return("$job->session"."_$job->id"."_$job->keyIdx".".log");
		}
		
		/* ---------------------------
		 *
		 */
		public function ExecuteJob($job)
		{
			$job->queueTime = time();
			$this->SaveJob($job);

			if(!isset($this->tmpFolder))
				$this->tmpFolder = "/tmp";			
			
			$logFolder = $this->tmpFolder;
			
			$logName = "$logFolder/".$this->getPhpChunkJobLogName($job);
			{
				$cmdLine = 'php -r "';
				$cmdLine.= 'require_once \'/opt/kaltura/app/batch/bootstrap.php\';';
///////////////
// DEBUG ONLY
//$dirName = dirname(__FILE__);
//$cmdLine.= 'require_once \''.$dirName.'/KChunkedEncodeUtils.php\';';
//$cmdLine.= 'require_once \'/tmp/KChunkedEncodeSessionManager.php\';';
//$cmdLine.= 'require_once \'/tmp/KChunkedEncodeMemcacheWrap.php\';';
//$cmdLine.= 'require_once \''.$dirName.'/KFFMpegMediaParser.php\';';
///////////////
				$cmdLine.= '\$rv=KChunkedEncodeMemcacheScheduler::ExecuteJobCommand(';
				$cmdLine.= '\''.($this->memcacheConfig['host']).'\',';
				$cmdLine.= '\''.($this->memcacheConfig['port']).'\',';
				$cmdLine.= '\''.($this->storeToken).'\',';
				$cmdLine.= $job->keyIdx.',';
				$cmdLine.= '\''.($this->tmpFolder).'\');';
				$cmdLine.= 'if(\$rv==false) exit(1);';
				$cmdLine.= '"';
			}
			$chunk_job_pid_file = $this->tmpFolder."/chunk_job_pid_".$job->session."_".$job->keyIdx.".log";
			$cmdLine.= " > $logName 2>&1 & echo $! > $chunk_job_pid_file";

			KalturaLog::log($cmdLine);

			$output = system($cmdLine, $rv);
			if($rv!=0) {
				$job->state = $job::STATE_FAIL;
				$this->SaveJob($job);
			}
			else {
				$job->process = (int)kFile::getFileContent($chunk_job_pid_file);
				kFile::unlink($chunk_job_pid_file);
			}
			KalturaLog::log("id:$job->id,keyIdx:$job->keyIdx,rv:$rv,process:$job->process,cmdLine:$cmdLine");
			return true;
		}
		
		/**
		 * This method executed one job fetched from the chunk encoding queue
		 * Please note that changing this methods signature may require adjusting the regex in KillJobsCommand method
		 *
		 * @param $host
		 * @param $port
		 * @param $token
		 * @param $jobIndex
		 * @param string $tmpPromptFolder
		 * @return bool
		 */
		public static function ExecuteJobCommand($host, $port, $token, $jobIndex, $tmpPromptFolder="/tmp",$ffmpegBin="ffmpeg",$ffprobeBin="ffprobe")
		{
			KalturaLog::log("host:$host, port:$port, token:$token, jobIndex:$jobIndex");
			$storeManager = new KChunkedEncodeMemcacheWrap($token);
				// 'flags=1' stands for 'compress stored data'
			$config = array('host'=>$host, 'port'=>$port, 'flags'=>1);
			$storeManager->Setup($config);
			
			if(!isset($jobIndex)) {
				return false;
			}

			$job = $storeManager->FetchJob($jobIndex);
			if($job===false)
				return false;
			
			kBatchUtils::tryLoadKconfConfig();
			
			$job->startTime = time();
			$job->process = getmypid();
			$job->hostname = gethostname();
			$job->state = $job::STATE_RUNNING;
			$storeManager->SaveJob($job);
			
			$outFilename = null;
			if(is_array($job->cmdLine)) {
				$cmdLine = $job->cmdLine[0];
				$outFilenames = isset($job->cmdLine[1]) ? $job->cmdLine[1] : null;;
				$outFilename = is_array($outFilenames) ? $outFilenames[0] : $outFilenames;
				//Added to support use cases where the shared file system is not NFS but rather a remote object storage.
				$sharedChunkPaths = isset($job->cmdLine[2]) ? $job->cmdLine[2] : null;
//				$outFilename = $job->cmdLine[1];
					/*
					 * Switch chunk generation to local disk folder (tmpPromptFolder)
					 * - replace the out file in tha cmdline to '/tmp' flolder
					 * - move all generated files back to the 'normal' tmp path
					 */
				$outFilenameInfo = null;
					// !!!TO DISABLE chunk-to-tmp flow, turn the 'true' to 'false' in condition bellow!!!
				if(isset($tmpPromptFolder) && true) {
					$outFilenameInfo = pathinfo($outFilename);
					$outFilename=$tmpPromptFolder.'/'.$outFilenameInfo['basename'];
					$cmdLine=str_replace($outFilenameInfo['dirname'], $tmpPromptFolder, $cmdLine);
						//Adjust the 'shared' mode outputs to 'chk-to-tmp' flow
					if(is_array($outFilenames)) {
						$storageFilenames = array();
						foreach($outFilenames as $idx=>$tmpName) {
							$storageFilenames[$idx] = $outFilenames[$idx];
							$tmpNameInfo = pathinfo($tmpName);
							$outFilenames[$idx]=$tmpPromptFolder.'/'.$tmpNameInfo['basename'];
						}
					}
				}
			}
			else
				$cmdLine = $job->cmdLine;
				/*
				 * Up to 2 attempts for chunk generation, in order to overcome possible
				 * zeroed frames issue, caused by too large GOPs in the source files 
				 */
			if(isset($job->maxExecTime)) {
				$maxExecTime = $job->maxExecTime;
				KalturaLog::log("maxExecTime:$maxExecTime");
			}
			for($i=0;$i<2;$i++) {
				if($i>0){
					$cmdLine = self::fixCmdLineBackOffset($cmdLine);
					KalturaLog::log("2nd attempt, due to empty frames, fixed cmdLine:$cmdLine");
					sleep(3);
				}
				exec($cmdLine,$op,$rv);
				$job->finishTime = time();
				if($rv!=0) {
					$job->state = $job::STATE_FAIL;
					$rvStr = "FAILED - rv($rv),";
					break;
				}
					// No need to check for empties for 1st chunk and audio chunks
				if($job->id==0 || strstr($outFilename,'.vid')===false)
					break;
				if(KFFMpegMediaParser::detectEmptyFrames($ffmpegBin, $ffprobeBin, $outFilename)===false)
					break;
					// Adjsut the maxExecTime according to attempt
				if(isset($job->maxExecTime)) {
					$job->maxExecTime = $maxExecTime*($i+2);
					$storeManager->SaveJob($job);
					KalturaLog::log("new maxExecTime:$job->maxExecTime");
				}
			}

			if($rv==0) {
				if(isset($outFilename) && strstr($outFilename,'.vid')) {
					$stat = new KChunkFramesStat();
						// VP9 / AV1 requires MP4 file container, they don't comply w/MPEGTS
						// X264 / X265 comply w/both MP4 & MPEGTS, but meanwhile we'll continue w/MPEGTS
					if((strstr($cmdLine,"libvpx-vp9")!==false) || (strstr($cmdLine,"libaom-av1")!==false))
						$rv = $stat->getDataMP4($outFilename,$ffprobeBin,$ffmpegBin,$tmpPromptFolder);
					else
						$rv = $stat->getDataMpegts($outFilename,$ffprobeBin,$ffmpegBin,$tmpPromptFolder);
					$job->stat = $stat;
				}

				if(isset($stat) && $stat->isEmpty()){
					$job->state = $job::STATE_FAIL;
					$rvStr = "FAILED - missing chunk stat,";
					$job->msg = "missing chunk stat";
				}
				else {
					$job->state = $job::STATE_SUCCESS;
					$rvStr = "SUCCESS -";
				}
			}
			
			//When working with remote (none nfs) shared storage we need to move the file to shared
			if($sharedChunkPaths){
				KalturaLog::log("Done running cmd line,moving file from [" . print_r($outFilenames, true) . "] to [" . print_r($sharedChunkPaths, true) . "]");
				$moveToFilenames = $sharedChunkPaths;
			}
					// !!!TO DISABLE chunk-to-tmp flow, turn the 'true' to 'false' in condition bellow!!!
			else if(isset($tmpPromptFolder) && isset($storageFilenames) && true)
				$moveToFilenames = $storageFilenames;
			
			$outFileSizes = array();
			if(isset($moveToFilenames)){
				$keys = array_keys($outFilenames);
				$movErrStr = null;
				foreach ($keys as $key) {
					$outFileSize = filesize($outFilenames[$key]);
					$outFileSizes[basename($outFilenames[$key])] = $outFileSize;
					KalturaLog::debug("Move file from [" . $outFilenames[$key] . "] to [" . $moveToFilenames[$key] . "] fileSize [$outFileSize]");
					if(kFile::checkFileExists($outFilenames[$key]) && !kFile::moveFile($outFilenames[$key], $moveToFilenames[$key])) {
						$job->state = $job::STATE_FAIL;
						$movErrStr.= ($storageFilenames[$key].",");
					}
				}
				if(isset($movErrStr)){
					$job->state = $job::STATE_FAIL;
					$rvStr = "FAILED - to mov files to remote ($movErrStr),";
					$rv=-1;
				}
			}

			$job->outFileSizes = $outFileSizes;
			$storeManager->SaveJob($job);
			
			KalturaLog::log("$rvStr elap(".($job->finishTime-$job->startTime)."),process($job->process),".print_r($job,1));
				// Move the PHP script log  from the local /tmp to the final storage
			if(isset($moveToFilenames)){
				$tmpFilenameInfo = pathinfo($outFilenames[0]);
				$movetToFilenameInfo = pathinfo($moveToFilenames[0]);
				$phpLogName = self::getPhpChunkJobLogName($job);
				$tmpFilename = $tmpFilenameInfo['dirname']."/$phpLogName";
				$moveToName  = $movetToFilenameInfo['dirname']."/$phpLogName";
				KalturaLog::log("move script log file:$tmpFilename, $moveToName");
				if(kFile::checkFileExists($tmpFilename))
					kFile::moveFile($tmpFilename, $moveToName);
			}
			return ($rv==0? true: false);
		}
		
		/**
		 * This method will kill all running jobs on the machine and will re-queue it for another machine to handle.
		 * Please note that if changing the ExecuteJobCommand method the running jobs regex may need to be adjusted accordingly
		 */
		public static function KillJobsCommand()
		{
			KalturaLog::log("Starting re-queue process for all active jobs");
			$storeManagers = array();
			$memcacheHost = $memcachePort =  $memcacheToken = null;
			
			//Command to fetch all the jobs that are currently running on the machine.
			KalturaLog::log("Fetching all keys for running chunk jobs");
			$runningJobs = explode("\n", shell_exec("ps -eo etimes,comm,args | grep [E]xecuteJobCommand"));
			$runningJobs = array_map('trim',$runningJobs);
			
			//Kill all running PHP tasks, this will kill the child FFMPEG jobs as well.
			KalturaLog::log("Killing all running jobs and re-adding them to the chunk convert queue: \n" . print_r($runningJobs, true));
			shell_exec("pkill -9 -f ExecuteJobCommand");
			
			$re = '/ExecuteJobCommand\(\'(?P<memcache_host>.*?)\',\'(?P<memcache_port>.*)\',\'(?P<memcache_token>.*)\',(?<job_idx>.*),/m';
			foreach($runningJobs as $runningJob)
			{
				if ($runningJob == "")
				{
					continue;
				}
				
				preg_match_all($re, $runningJob, $matches);
				if (!count($matches))
				{
					KalturaLog::log("No matches found for cmd [$runningJob]");
					continue;
				}
				
				$memcacheHost = $matches['memcache_host'][0];
				$memcachePort = $matches['memcache_port'][0];
				//memcacheToken is not mandatory the general worker may run with empty token key
				$memcacheToken = isset($matches['memcache_token'][0]) ? $matches['memcache_token'][0] : null;
				if(!isset($storeManagers[$memcacheToken]) && $memcachePort && $memcacheHost)
				{
					KalturaLog::log("host:$memcacheHost, port:$memcachePort, token:$memcacheToken");
					$storeManager = new KChunkedEncodeMemcacheWrap($memcacheToken);
					$storeManager->Setup(array('host'=>$memcacheHost, 'port'=>$memcachePort, 'flags'=>1));
					$storeManagers[$memcacheToken] = $storeManager;
				}
				elseif(isset($storeManagers[$memcacheToken]))
				{
					KalturaLog::log("Storage manager found will reuse it");
					$storeManager = $storeManagers[$memcacheToken];
				}
				else
				{
					KalturaLog::log("Missing Storage manager token [$memcacheToken] params[$memcacheHost] [$memcachePort]");
					continue;
				}
				
				$jobIndex = $matches['job_idx'][0];
				$job = $storeManager->FetchJob($jobIndex);
				$job->state = $job::STATE_RETRY;
				$storeManager->SaveJob($job);
				KalturaLog::log("Refreshed job [$jobIndex]");
			}
		}

		/* ---------------------------
		 *
		 */
		protected function setReadIndex($readIndex)
		{
			$maxTry=10;
			for($try=0; $try<$maxTry; $try++) {
				$rv = $this->set($this->getReadIndexKeyName(),$readIndex,0);
				if($rv!==false){
					return $rv;
				}
				KalturaLog::log("Attempt($try) to set RD($readIndex)");
				usleep(rand(0,100000));
			}
			return false;
		}

		/* ---------------------------
		 * fixCmdLineBackOffset
		 *	increases the default seek-to back-offset in order to handle source's large GOP cases,
		 * 	that are located on chunk boundary, preventing the ability for precise seek-to
		 */
		private static function fixCmdLineBackOffset($cmdLine)
		{
			$cmdLineArr = explode(" ",$cmdLine);
				// Remove spaces
			foreach($cmdLineArr as $idx=>$val){
				if(strlen(trim($cmdLineArr[$idx]))==0){
					unset($cmdLineArr[$idx]);
				}
			}
			$keys = array_keys($cmdLineArr, "-ss" );
			if(count($keys)==2){
				$startFromIdx = $keys[0]+1;
				$backOffsIdx  = $keys[1]+1;
				$startFrom = $cmdLineArr[$startFromIdx];
				$backOffs  = $cmdLineArr[$backOffsIdx];
				
				$fix = $backOffs*2;
				$cmdLineArr[$startFromIdx] = $startFrom - $fix;
				$cmdLineArr[$backOffsIdx]  = $backOffs  + $fix;
				KalturaLog::log("Fixed startfrom:$cmdLineArr[$startFromIdx] (orig $startFrom), backOffs:$backOffs($cmdLineArr[$backOffsIdx])");
			}
			return (implode(" ",$cmdLineArr));
		}
		
		/* ---------------------------
		 * incrementReadIndex
		 */
		protected function incrementReadIndex()	{
			$idx=$this->increment($this->getReadIndexKeyName());
			return $idx;
		}
	}
	
	/*****************************
	 * End of KChunkedEncodeMemcacheScheduler
	 *****************************/

	

