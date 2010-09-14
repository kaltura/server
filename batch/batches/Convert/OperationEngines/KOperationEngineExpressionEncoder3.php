<?php
/**
 * 
 * @package Scheduler
 * @subpackage Conversion
 */
class KOperationEngineExpressionEncoder3 extends KOperationEngine
{
	/**
	 * @var string
	 */
	protected $outDir;
	
	/**
	 * @var string
	 */
	protected $destFileName;
	
	/**
	 * @param string $outDir
	 */
	public function __construct($cmd, $destFileName, $outDir)
	{
		parent::__construct($cmd);
		
		$this->destFileName = $destFileName;
		$this->outDir = $outDir;
	}
	
	protected function getCmdLine()
	{
		$xml = file_get_contents($this->configFilePath);
		$xml = str_replace(KDLCmdlinePlaceholders::OutDir, $this->outDir, $xml);
		file_put_contents($this->configFilePath, $xml);

		$this->addToLogFile("Config File Path: $this->configFilePath");
		$this->addToLogFile($xml);
		
		$exec_cmd = $this->cmd . " " . 
			str_replace ( 
				array(KDLCmdlinePlaceholders::InFileName, KDLCmdlinePlaceholders::OutDir, KDLCmdlinePlaceholders::ConfigFileName), 
				array($this->inFilePath, $this->outDir, $this->configFilePath),
				$this->operator->command);
				
		$exec_cmd .= " >> \"{$this->logFilePath}\" 2>&1";
		
		return $exec_cmd;
	}
	
	/* (non-PHPdoc)
	 * @see batches/Convert/OperationEngines/KOperationEngine#doOperation()
	 */
	protected function doOperation()
	{
		parent::doOperation();
		
		$this->parseCreatedFiles();
		foreach($this->outFilesPath as $outFile)
		{
			$this->addToLogFile("media info [$outFile]");
			$this->logMediaInfo($outFile);
		}
	}
	
	protected function parseCreatedFiles()
	{
		$xmlPath = $this->outDir . DIRECTORY_SEPARATOR . $this->destFileName . '.ism';
		KalturaLog::info("Parse created files from ism[$xmlPath]");
		
		// in case of wma
		if(!file_exists($xmlPath))
		{
			KalturaLog::info("ism file[$xmlPath] doesn't exist");
			$wmaPath = $this->outDir . DIRECTORY_SEPARATOR . $this->destFileName . '.wma';
			if(file_exists($wmaPath))
			{
				KalturaLog::info("wma file[$wmaPath] found");
				$this->outFilesPath[0] = $wmaPath;
			}
			
			return;
		}
		
		$xml = file_get_contents($xmlPath);
		$xml = mb_convert_encoding($xml, 'ASCII', 'UTF-16');
		
		KalturaLog::debug("ism content:\n$xml");
		
		$arr = null;
		if(preg_match('/(<smil[\s\w\W]+<\/smil>)/', $xml, $arr))
			$xml = $arr[1];
		file_put_contents($xmlPath, $xml);
		
		//echo $xml;
		$doc = new DOMDocument();
		$doc->loadXML($xml);
		$videoEntities = $doc->getElementsByTagName('video');
		foreach($videoEntities as $videoEntity)
		{
			$src = $this->outDir . DIRECTORY_SEPARATOR . $videoEntity->getAttribute("src");
			$bitrate = $videoEntity->getAttribute("systemBitrate") / 1000;
			
			KalturaLog::debug("Media found in ism bitrate[$bitrate] source[$src]");
			$this->outFilesPath[$bitrate] = $src;
		}
	}
}
