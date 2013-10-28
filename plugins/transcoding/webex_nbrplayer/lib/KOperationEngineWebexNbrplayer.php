<?php
/**
 * @package plugins.webexNbrplayer
 * @subpackage lib
 */
class KOperationEngineWebexNbrplayer  extends KSingleOutputOperationEngine
{

	/**
	 * @var string
	 */
const WebexCfgTemplate = "
[Console]
inputfile=
; media options - WMV,MP4
media=
showui=0
PCAudio=0
[UI]
video=1
chat=1
qa=1
notes=1
polling=1
ft=1
largeroutline=1
[__webexTargetFormat__]
outputfile=
;width=																			
;height=
;videocodec options: 
;  Windows Media Video 9 
;  Windows Media Video 9 Screen
videocodec=
;audiocodec options:
;  Windows Media Audio 9.2 Lossless
;  Windows Media Audio 9.2
;  Windows Media Audio 10 Professional
audiocodec=
videoformat=default
audioformat=default
videokeyframes=
maxstream=
";

public function buildCfgFile($inputFile, $outputFile, $format=null, 
		$width=null, $height=null, $keyFramesInSec=null, $bitrate=null,
		$videoCodec=null, $audioCodec=null)
{
	if(is_null($format)) $format="WMV";
	if($format=="WMV"){
		if(is_null($width))  $width=1280;
		if(is_null($height)) $height=960;
		if(is_null($keyFramesInSec)) $keyFramesInSec=4;
		if(is_null($bitrate)) $bitrate=2000;
		if(is_null($videoCodec)) $videoCodec = "Windows Media Video 9";
		if(is_null($audioCodec)) $audioCodec = "Windows Media Audio 10 Professional";
	}
	else if($format=="MP4"){
		
	}
	$cfg = str_replace (
		array("inputfile=","outputfile=","media=","__webexTargetFormat__","width=","height=","videocodec=","audiocodec=","videokeyframes=","maxstream="),
		array("inputfile=".$inputFile,"outputfile=".$outputFile,"media=".$format,$format,"width=".$width,"height=".$height,
				"videocodec=".$videoCodec,"audiocodec=".$audioCodec,"videokeyframes=".$keyFramesInSec,"maxstream=".$bitrate),
		self::WebexCfgTemplate);
	KalturaLog::log($cfg);
	return $cfg;
}

//	protected $outDir;
//	protected $configData;
	
	/**
	 * @var string
	 */
	
	/**
	 * @param string $outDir
	 */
	public function __construct($cmd, $outFilePath)
	{
		parent::__construct($cmd,$outFilePath);
		KalturaLog::info(": cmd($cmd), outFilePath($outFilePath)");
	}

	protected function getCmdLine()
	{
		$cfgStr = $this->buildCfgFile($this->inFilePath, $this->outFilePath);
		$this->configFilePath = $this->outFilePath.".cfg";
		file_put_contents($this->configFilePath, $cfgStr);
		$this->addToLogFile("Webex CFG:\n*******\n$cfgStr\n*******\n");

		$exeCmd =  parent::getCmdLine();
		KalturaLog::info("command line: $exeCmd");
		KalturaLog::info(print_r($this,true));
		return $exeCmd;
	}

	public function configure(KalturaConvartableJobData $data, KalturaBatchJob $job)
	{
		parent::configure($data, $job);
		KalturaLog::info("taskConfig-->".print_r(KBatchBase::$taskConfig,true)."\ndata->".print_r($data,true));
	}
	
	public function operate(kOperator $operator = null, $inFilePath, $configFilePath = null)
	{
			/*
			 * Creating unique output folder for nbrPlay/Webex sessions.
			 * This is required in order to support concurrent conversion sessions,
			 * because the nbrPlay tool generates temp files with the same name.
			 * Upon completion move the generated file into the 'regular' outFilePath
			 */
		$saveOutPath = $this->outFilePath;	
		
		$path_parts = pathinfo($this->outFilePath);
		$outDir = realpath($path_parts['dirname']);
			/*
			 * The temp folder name
			 */
		$tempFolder = "$outDir/".$path_parts['basename'].".webex_temp_folder";
		$tempOutPath = "$tempFolder/".$path_parts['basename'];
		if(!file_exists($tempFolder)){
			$oldUmask = umask(00);
			$result = @mkdir($tempFolder, 0777, true);
			umask($oldUmask);
		}
			/*
			 * Switch to temp forlder
			 */
		$this->outFilePath = $tempOutPath;
		$rv = parent::operate($operator, $inFilePath, $configFilePath);
		
			/*
			 * Restore the original
			 */
		if(file_exists($tempOutPath)){
			$outFilelist = kFile::dirList($tempFolder);
			if(isset($outFilelist) && count($outFilelist)>0){
				foreach ($outFilelist as $fileName){
					for($tries=0; $tries<5; $tries++){
						$toFile = "$outDir/".pathinfo($fileName, PATHINFO_BASENAME);
						$rv = kFile::moveFile($fileName, $toFile);
						if(!file_exists($fileName)){
							break;
						}
						KalturaLog::err("Failed to move ($fileName) to ($toFile)");
						Sleep(60);
					}
				}
				Sleep(60);
				rmdir($tempFolder);
			}
		}
		$this->outFilePath = $saveOutPath;
		
		return $rv;
	}
		
}

		
