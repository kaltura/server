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

	public function configure(KSchedularTaskConfig $taskConfig, KalturaConvartableJobData $data)
	{
		parent::configure($taskConfig, $data);
		
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
		$sleepTimes = 50;
		$sleepSeconds = 3;
		while (!file_exists(realpath($tmpFile)) && $sleepTimes > 0) {
			sleep($sleepSeconds);
			$sleepTimes--;
			clearstatcache();
		}
		
		if (!file_exists(realpath($tmpFile))) {
			throw new KOperationEngineException('Temp PDF Creator file not found ['.$tmpFile.'] output file ['.$this->outFilePath.']');
		}else{
			KalturaLog::notice('document temp  found ['.$tmpFile.'] output file ['.$this->outFilePath.']'); 
		}
		
		
		//TODO: RENAME - will not be needed once PDFCreator can work with a configurations file	
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