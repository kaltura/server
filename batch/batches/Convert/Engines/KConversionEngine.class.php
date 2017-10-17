<?php
/**
 * base class for the real ConversionEngines in the system - ffmpeg,menconder and flix. 
 * 
 * @package Scheduler
 * @subpackage Conversion.engines
 */
abstract class KConversionEngine
{
	const COMMAND_SEPARATOR = "|||";
	const COMMAND_KEY_SEPARATOR = "@@@";
	
	const MILTI_COMMAND_LINE_SEPERATOR = ';;;';
	const FAST_START_SIGN = 'FS';
	
	
	/**
	 * @var string
	 */
	protected $inFilePath = null;
	
	/**
	 * @var string
	 */
	protected $outFilePath = null;
	
	/**
	 * Should be empty string by default
	 * @var string
	 */
	protected $configFilePath = '';
	
	/**
	 * @var string
	 */
	protected $logFilePath = null;
	
	/**
	 * @var bool
	 */
	protected $mediaInfoEnabled = false;
	
	
	/**
	 * Will return the proper engine depending on the type (KalturaConversionEngineType)
	 *
	 * @param int $type
	 * @return KConversionEngine
	 */
	public static function getInstance($type)
	{
		$engine =  null;
		
		switch ($type )
		{
			case KalturaConversionEngineType::FFMPEG:
				$engine = new KConversionEngineFfmpeg();
				break;
			case KalturaConversionEngineType::CHUNKED_FFMPEG:
				$engine = new KConversionEngineChunkedFfmpeg();
				break;
			case KalturaConversionEngineType::MENCODER:
				$engine = new KConversionEngineMencoder();
				break;
			case KalturaConversionEngineType::ON2:
				$engine = new KConversionEngineFlix();
				break;
			case KalturaConversionEngineType::ENCODING_COM :
				$engine = new KConversionEngineEncodingCom();
				break;
			case KalturaConversionEngineType::FFMPEG_AUX:
				$engine = new KConversionEngineFfmpegAux();
				break;
			case KalturaConversionEngineType::EXPRESSION_ENCODER3:
				$engine = new KConversionEngineExpressionEncoder3();
				break;
			case KalturaConversionEngineType::FFMPEG_VP8:
				$engine = new KConversionEngineFfmpegVp8();
				break;
				
			default:
				$engine =  null;
		}
		
		return $engine;
	}

	
	abstract public function getCmd();
	abstract public function getName();
	abstract public function getType();
	abstract public function simulate ( KalturaConvartableJobData $data );
	
	/**
	 * $start_params_index - the index of the kConversionParams in the kConversionCommand from which to start from. might not start at 0
	 * $end_params_index - the index of the kConversionParams in the kConversionCommand to which to end at. -1 - the end
	 * 
	 * @param KalturaConvartableJobData $data
	 * @return array 
	 */
	abstract public function convert ( KalturaConvartableJobData &$data );
	
	public function getLogData()
	{
		return file_get_contents($this->logFilePath);
	}
	
	/**
	 * @return string
	 */
	public function getLogFilePath()
	{
		return $this->logFilePath;
	}

	/**
	 * @param array $commandLinesStr
	 * @return array
	 */
	public function getCmdArray($commandLinesStr)
	{
		$cmd_line_arr = explode ( self::COMMAND_SEPARATOR , $commandLinesStr );
		$ret = array();
		foreach($cmd_line_arr as $cmd_line)
		{
			$lineArr = explode( self::COMMAND_KEY_SEPARATOR, $cmd_line, 2);
			$ret[$lineArr[0]] = $lineArr[1];
		}
		return $ret;
	}
	
	/**
	 * @param boolean $add_log
	 * @return string
	 */
	protected function getQuickStartCmdLine($add_log)
	{
		/*
		 * If there is 'fastStartWithMp4box' set in ini- use MP4Box for mp4 faststart adjustment. 
		 * The mp4box removes unrequired menu metadata from the file, that caused rtmp streaming problems 
		 */
		if(isset(KBatchBase::$taskConfig->params->mp4boxCmd) 
		&& isset(KBatchBase::$taskConfig->params->fastStartWithMp4box) && KBatchBase::$taskConfig->params->fastStartWithMp4box==1) {
			$inFile = $this->inFilePath;
			$tmpFile = "$inFile.tmp";
			$cmd_line = "-add $tmpFile ".$this->outFilePath." && mv $tmpFile $inFile";
			$exe = "mv $inFile $tmpFile && ".KBatchBase::$taskConfig->params->mp4boxCmd;
		}
		else {
			$cmd_line = "__inFileName__ __outFileName__";
			$exe = KBatchBase::$taskConfig->params->fastStartCmd;
		}
		// I have commented out the audio parameters so we don't decrease the quality - it stays as-is
		$exec_cmd = "$exe " . 
			str_replace ( 
				array(KDLCmdlinePlaceholders::InFileName, KDLCmdlinePlaceholders::OutFileName, KDLCmdlinePlaceholders::ConfigFileName), 
				array($this->inFilePath, $this->outFilePath, $this->configFilePath),
				$cmd_line);
				
		if ( $add_log )
		{
			// redirect both the STDOUT & STDERR to the log
			$exec_cmd .= " >> \"{$this->logFilePath}\" 2>&1";
		}
		
		return $exec_cmd;
	}	
	
	/**
	 * derived classes can override this is they create the command lines in a different way 
	 * 
	 * @param string $cmd_line
	 * @param boolean $add_log
	 * @return string
	 */
	protected function getCmdLine ($cmd_line , $add_log )
	{
		// I have commented out the audio parameters so we don't decrease the quality - it stays as-is
		$binName=$this->getCmd();
		$exec_cmd = $binName . " " . 
			str_replace ( 
				array(KDLCmdlinePlaceholders::InFileName, KDLCmdlinePlaceholders::OutFileName, KDLCmdlinePlaceholders::ConfigFileName, KDLCmdlinePlaceholders::BinaryName), 
				array($this->inFilePath, $this->outFilePath, $this->configFilePath, $binName),
				$cmd_line);
				
		if ( $add_log )
		{
			// redirect both the STDOUT & STDERR to the log
			$exec_cmd .= " >> \"{$this->logFilePath}\" 2>&1";
		}
		
		return $exec_cmd;
	}
	
	/**
	 * @param bool $enabled
	 */
	public function setMediaInfoEnabled($enabled)
	{
		$this->mediaInfoEnabled = $enabled;
	}
	
	/**
	 * @param string $log_file
	 * @param string $file
	 */
	protected function logMediaInfo($log_file, $file)
	{
		if(!$this->mediaInfoEnabled)
			return;
			
		try
		{			
			if ( file_exists ( $file ))
			{
				$media_info = shell_exec("mediainfo ".realpath($file));
				$this->addToLogFile ( $log_file ,$media_info ) ;
			}
			else
			{
				$this->addToLogFile ( $log_file ,"Cannot find file [$file]" ) ;
			}
		}
		catch ( Exception $ex ) { /* do nothing */ }		
	}
	
	/**
	 * ne = not- empty
	 * 
	 * @param string $param_name
	 * @param string $param_value
	 * @param string $default_value
	 * @return string
	 */
	protected static function ne ( $param_name , $param_value , $default_value = null)
	{
		if ( $param_value ) return $param_name . $param_value;
		if ( ! is_null($default_value ) ) return $default_value;
		else return "";
	}

	/**
	 * @param string $file_name
	 * @param string $str
	 */
	protected function addToLogFile ( $file_name , $str )
	{
		KalturaLog::debug($str);
		$extra_content = "\n\n----------------------\n$str\n----------------------\n\n";
		kFile::appendToFile($file_name, $extra_content);
	}
	
	protected function getSrcActualPathFromData($data)
	{
		$srcFileSyncDescriptor = reset($data->srcFileSyncs);
		if($srcFileSyncDescriptor)
			return $srcFileSyncDescriptor->actualFileSyncLocalPath;		
		else
			return null;
	}
	
	protected function getSrcRemoteUrlFromData($data)
	{
		$srcFileSyncDescriptor = reset($data->srcFileSyncs);
		if($srcFileSyncDescriptor)
			return $srcFileSyncDescriptor->fileSyncRemoteUrl;		
		else
			return null;
	}

}


/**
 * @package Scheduler
 * @subpackage Conversion
 *
 */
class KConversioEngineResult
{
	public $exec_cmd ;
	public $conversion_string;
	
	/**
	 * @param string $exec_cmd
	 * @param string $conversion_string
	 */
	public function __construct( $exec_cmd , $conversion_string )
	{
		$this->exec_cmd = $exec_cmd;
		$this->conversion_string = $conversion_string;
	}
}


