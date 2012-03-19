<?php

class KOperationEnginePdfCreator extends KSingleOutputOperationEngine
{
	/**
	 * @var string
	 */
	private $orgInFilePath = '';
	
	/**
	 * @var KalturaPdfFlavorParamsOutput
	 */
	private $flavorParamsOutput;

	//old office files prefix
	const OLD_OFFICE_SIGNATURE = "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1";
	
	public function configure(KSchedularTaskConfig $taskConfig, KalturaConvartableJobData $data, KalturaClient $client)
	{
		parent::configure($taskConfig, $data, $client);
		
		$this->flavorParamsOutput = $data->flavorParamsOutput;
		KalturaLog::debug("document : this [". print_r($this, true)."]"); 
	}
	
	public function operate(kOperator $operator = null, $inFilePath, $configFilePath = null)
	{
		KalturaLog::debug("document : operator [". print_r($operator, true)."] inFilePath [$inFilePath]"); 
		if ($configFilePath) {
			$configFilePath = realpath($configFilePath);
		}
		
		// bypassing PDF Creator for source PDF files
		$inputExtension = strtolower(pathinfo($inFilePath, PATHINFO_EXTENSION));
		if (($inputExtension == 'pdf') && (!$this->flavorParamsOutput->readonly)) {
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
				return;
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
		//checks if $realInFilePath is an old office document with a new extension ('pptx|docx|xlsx')
		//if $realInFilePath is not the fileSync itself ($uniqueName = true) , rename the file by removing the 'x' from the extension.		
		if ($uniqueName && in_array ( $ext, $newOfficeExtensions ) && $filePrefix == self::OLD_OFFICE_SIGNATURE) {
			$RealInFilePathWithoutX = substr ( $realInFilePath, 0, - 1 );
			if (rename ( $realInFilePath, $RealInFilePathWithoutX )){
				KalturaLog::notice("renamed file [$realInFilePath] to [$RealInFilePathWithoutX]");
				$realInFilePath = $RealInFilePathWithoutX;
			}
		}
		
		parent::operate($operator, $realInFilePath, $configFilePath);
		
		if ($uniqueName) {
			@unlink($tmpUniqInFilePath);
		}
		//TODO: RENAME - will not be needed once PDFCreator can work with a configurations file
		if (($inputExtension == 'pdf') && ($this->flavorParamsOutput->readonly == true)){
			$tmpFile = $this->outFilePath.'.pdf';
		}else{
			$tmpFile = kFile::replaceExt(basename($realInFilePath), 'pdf');
			$tmpFile = dirname($this->outFilePath).'/'.$tmpFile;
		}
		
		// sleeping while file not ready, since PDFCreator exists a bit before the file is actually ready
		$sleepTimes = 10;
		$sleepSeconds = 1;
		while (!file_exists(realpath($tmpFile)) && $sleepTimes > 0) {
			sleep($sleepSeconds);
			$sleepTimes--;
			clearstatcache();
		}
		
		if (!file_exists(realpath($tmpFile))) {
			throw new kTemporaryException('Temp PDF Creator file not found ['.$tmpFile.'] output file ['.$this->outFilePath.']');
		}else{
			KalturaLog::notice('document temp  found ['.$tmpFile.'] output file ['.$this->outFilePath.']'); 
		}
		
		
		//TODO: RENAME - will not be needed once PDFCreator can work with a configurations file	
		$sleepTimes = 50;
		$sleepSeconds = 3;
		$tmpFile = realpath($tmpFile);
		while (!rename($tmpFile, $this->outFilePath) && $sleepTimes > 0) {
			sleep($sleepSeconds);
			$sleepTimes--;
			clearstatcache();
		}
		
		if (!file_exists($this->outFilePath)) {
			$error = '';
			if (function_exists('error_get_last')) {
				$error = error_get_last();
			}
			throw new KOperationEngineException('Cannot rename file ['.$tmpFile.'] to ['.$this->outFilePath.'] - ['.$error.']');
		}
		
	}
		
	
}