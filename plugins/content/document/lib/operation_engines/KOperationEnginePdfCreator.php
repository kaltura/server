<?php

/**
 * @package plugins.document
 * @subpackage lib
 */
class KOperationEnginePdfCreator extends KSingleOutputOperationEngine
{
	/**
	 * @var string
	 */
	private $orgInFilePath = '';
	
	
	//old office files prefix
	const OLD_OFFICE_SIGNATURE = "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1";
	
	//this will be the default value if the parameter is not set in the task's configuration
	const DEFAULT_FILE_UNLOCK_RETRIES = 100;
	
	//this will be the default value if the parameter is not set in the task's configuration
	const DEFAULT_FILE_UNLOCK_INTERVAL = 3;
	
	//this will be the default value if it not set in the task's configuration
	const DEFAULT_SLEEP_TIMES = 10;
	
	//this will be the default value if it not set in the task's configuration
	const DEFAULT_SLEEP_SECONDS = 2;
	
	// this will be the default value if it is not set in task's configuration under - killPopupsPath
	const DEFAULT_KILL_POPUPS_PATH = "c:/temp/killWindowsPopupsLog.txt";
	
	public function operate(kOperator $operator = null, $inFilePath, $configFilePath = null)
	{
		KalturaLog::debug("document : operator [". print_r($operator, true)."] inFilePath [$inFilePath]"); 
		if ($configFilePath) {
			$configFilePath = realpath($configFilePath);
		}
		
		// bypassing PDF Creator for source PDF files
		$inputExtension = strtolower(pathinfo($inFilePath, PATHINFO_EXTENSION));
		if (($inputExtension == 'pdf') && (!$this->data->flavorParamsOutput->readonly)) {
			KalturaLog::notice('Bypassing PDF Creator for source PDF files');
			if (!@copy($inFilePath, $this->outFilePath)) {
				$error = '';
				if (function_exists('error_get_last')) {
					$error = error_get_last();
				}
				throw new KOperationEngineException('Cannot copy PDF file ['.$this->inFilePath.'] to ['.$this->outFilePath.'] - ['.$error.']');
			}
			else {
				// PDF input file copied as is to output file
				return true;
			}
		}
				
		// renaming with unique name to allow conversion 2 conversions of same input file to be done together (PDF+SWF)
		$tmpUniqInFilePath = dirname($inFilePath).'/'.uniqid().'_'.basename($inFilePath);
		$realInFilePath = '';
		$uniqueName = false;
		if (@copy($inFilePath, $tmpUniqInFilePath)) {
			$realInFilePath = realpath($tmpUniqInFilePath);
			$uniqueName = true;
		}
		else {
			KalturaLog::notice('Could not rename input file ['.$inFilePath.'] with a unique name ['.$tmpUniqInFilePath.']');
			$realInFilePath = realpath($inFilePath);
		}
		
		$filePrefix = file_get_contents ( $realInFilePath, false, null, 0, strlen ( self::OLD_OFFICE_SIGNATURE ) );
		$path_info = pathinfo ( $realInFilePath );
		$ext = $path_info ['extension'];
		$newOfficeExtensions = Array ('pptx', 'docx', 'xlsx' );
		$ext =  strtolower($ext);
		//checks if $realInFilePath is an old office document with a new extension ('pptx|docx|xlsx')
		//if $realInFilePath is not the fileSync itself ($uniqueName = true) , rename the file by removing the 'x' from the extension.		
		if ($uniqueName && in_array ( $ext, $newOfficeExtensions ) && $filePrefix == self::OLD_OFFICE_SIGNATURE) {
			$RealInFilePathWithoutX = substr ( $realInFilePath, 0, - 1 );
			if (rename ( $realInFilePath, $RealInFilePathWithoutX )){
				KalturaLog::notice("renamed file [$realInFilePath] to [$RealInFilePathWithoutX]");
				$realInFilePath = $RealInFilePathWithoutX;
			}
		}
		
		$finalOutputPath = $this->outFilePath;
		
		if (($inputExtension == 'pdf') && ($this->data->flavorParamsOutput->readonly == true)){
			$tmpFile = $this->outFilePath.'.pdf';
		}else{
			$tmpFile = kFile::replaceExt(basename($realInFilePath), 'pdf');
			$tmpFile = dirname($this->outFilePath).'/'.$tmpFile;
		}
		$this->outFilePath = $tmpFile;
		
		// Create popups log file
		$killPopupsPath = $this->getKillPopupsPath();
		if(file_exists($killPopupsPath))
			unlink($killPopupsPath);
		
		parent::operate($operator, $realInFilePath, $configFilePath);

		$this->outFilePath = $finalOutputPath;
		
		if ($uniqueName) {
			@unlink($tmpUniqInFilePath);
		}
		
		$sleepTimes = KBatchBase::$taskConfig->fileExistReties;
		if (!$sleepTimes){
			$sleepTimes = self::DEFAULT_SLEEP_TIMES;
		}
		
		$sleepSeconds = KBatchBase::$taskConfig->fileExistInterval;
		if (!$sleepSeconds){
			$sleepSeconds = self::DEFAULT_SLEEP_SECONDS;
		}
		
		// sleeping while file not ready, since PDFCreator exists a bit before the file is actually ready
		while (!file_exists(realpath($tmpFile)) && $sleepTimes > 0) {
			sleep($sleepSeconds);
			$sleepTimes--;
			clearstatcache();
		}
		
		// Read popup log file
		if(file_exists($killPopupsPath)) {
			$data = file_get_contents($killPopupsPath);
			KalturaLog::notice("Convert popups warnings - " . $data);
			$this->message = $data;

			unlink($killPopupsPath);
		}
		
		if (!file_exists(realpath($tmpFile))) {
			throw new kTemporaryException('Temp PDF Creator file not found ['.$tmpFile.'] output file ['.$this->outFilePath.'] 
					Convert Engine message [' . $this->message . ']');
		}else{
			KalturaLog::notice('document temp  found ['.$tmpFile.'] output file ['.$this->outFilePath.']'); 
		}
		
		
		// $this->validateOutput($inFilePath, $tmpFile);
		
		$fileUnlockRetries = KBatchBase::$taskConfig->params->fileUnlockRetries ;
		if(!$fileUnlockRetries){
			$fileUnlockRetries = self::DEFAULT_FILE_UNLOCK_RETRIES;
		}
		$fileUnlockInterval = KBatchBase::$taskConfig->params->fileUnlockInterval;
		if(!$fileUnlockInterval){
			$fileUnlockInterval = self::DEFAULT_FILE_UNLOCK_INTERVAL; 
		}
		$tmpFile = realpath($tmpFile);
		while (!rename($tmpFile, $this->outFilePath) && $fileUnlockRetries > 0) {
			sleep($fileUnlockInterval);
			$fileUnlockRetries--;
			clearstatcache();
		}
		
		if (!file_exists($this->outFilePath)) {
			$error = '';
			if (function_exists('error_get_last')) {
				$error = error_get_last();
			}
			throw new KOperationEngineException('Cannot rename file ['.$tmpFile.'] to ['.$this->outFilePath.'] - ['.$error.']');
		}
		
		return true;
		
	}
	
	private function validateOutput($inFilePath, $outFilePath)
	{
		$pdfInfo = KBatchBase::$taskConfig->params->pdfInfo;
		$inputExtension = strtolower(pathinfo($inFilePath, PATHINFO_EXTENSION));
		if($inputExtension == 'pdf') {
			$inputNum = $this->getNumberOfPages($pdfInfo, $inFilePath);
			$outputNum = $this->getNumberOfPages($pdfInfo, $outFilePath);
			if($inputNum != $outputNum)
				throw new KOperationEngineException("Output file doesn't match expected page count (input: $inputNum, output: $outputNum) ");
		}
	}
	
	private function getNumberOfPages($pdfInfo, $file) 
	{
		$cmd = $pdfInfo . " " . realpath($file);
		exec($cmd, $output);
		foreach($output as $cur) {
			if(preg_match('/Pages:\s*(\d+)/' , $cur, $matches))
				return $matches[1];
		}
		return null;
	}
		
	private function getKillPopupsPath() 
	{
		$killPopupsPath = KBatchBase::$taskConfig->params->killPopupsPath;
		if(!$killPopupsPath){
			$killPopupsPath = self::DEFAULT_KILL_POPUPS_PATH;
		}
		return $killPopupsPath;
	}
	
}