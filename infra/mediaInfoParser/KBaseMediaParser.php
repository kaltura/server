<?php
/**
 * @package infra
 * @subpackage Media
 */
abstract class KBaseMediaParser
{
	const MEDIA_PARSER_TYPE_MEDIAINFO = 0;
	const MEDIA_PARSER_TYPE_FFMPEG = 1;
	
	/**
	 * @var string
	 */
	protected $filePath;
	
	/**
	 * @param string $type
	 * @param string $filePath
	 * @param KSchedularTaskConfig $taskConfig
	 * @return KBaseMediaParser
	 */
	public static function getParser($type, $filePath, KSchedularTaskConfig $taskConfig)
	{
		if($type=="RemoteMediaInfo"){
KalturaLog::debug("RemoteMediaInfo===>type:$type,filePath:$filePath");
			return new KRemoteMediaInfoParser($filePath, "RemoteMediaInfoService");
		}
		switch($type)
		{
			case self::MEDIA_PARSER_TYPE_MEDIAINFO:
				return new KMediaInfoMediaParser($filePath, $taskConfig->params->mediaInfoCmd);
				
			case self::MEDIA_PARSER_TYPE_FFMPEG:
				return new KFFMpegMediaParser($filePath, $taskConfig->params->FFMpegCmd);
				
			default:
				return KalturaPluginManager::loadObject('KBaseMediaParser', $type, func_get_args());
		}
	}
	
	/**
	 * @param string $filePath
	 */
	public function __construct($filePath)
	{
		if (!file_exists($filePath))
			throw new Exception("File not found at [$filePath]");
			
		$this->filePath = $filePath;
	}
	
	/**
	 * @return KalturaMediaInfo
	 */
	public function getMediaInfo()
	{
		$output = $this->getRawMediaInfo();
		return $this->parseOutput($output);
	}
	
	/**
	 * @return string
	 */
	public function getRawMediaInfo()
	{
		$cmd = $this->getCommand();
		KalturaLog::debug("Executing '$cmd'");
		$output = shell_exec($cmd);
		if (trim($output) === "")
			throw new Exception("Failed to parse media using " . get_class($this));
			
		return ($output);
	}
	
	/**
	 * @return string
	 */
	protected abstract function getCommand();
		
	/**
	 * 
	 * @param string $output
	 * @return KalturaMediaInfo
	 */
	protected abstract function parseOutput($output);
}

/**
 * @package infra
 * @subpackage Media
 */
class KRemoteMediaInfoParser extends KMediaInfoMediaParser
{
	/**
	 * @param string $filePath
	 * @param string $cmdPath
	 */
	public function __construct($filePath, $cmdPath = 'mediainfo')
	{
		$this->cmdPath = $cmdPath;
		$this->filePath = $filePath;
//		Can not run the 'parent::__construct' because it throws exception on missing file.
//		parent::__construct($filePath);
	}

	/**
	 * @return string
	 */
	public function getRawMediaInfo()
	{
		$cmd = $this->getCommand();
KalturaLog::debug("Executing '$cmd'");
$mediaInfoSample = "
General
Complete name                    : C:\\xampp\htdocs\mvdr6t454m.mov
Format                           : MPEG-4
Format profile                   : QuickTime
Codec ID                         : qt  
File size                        : 66.6MiB 
Duration                         : 2mn 25s
Overall bit rate                 : 3 851 Kbps
Encoded date                     : UTC 2009-08-21 18:45:38
Tagged date                      : UTC 2009-08-21 18:45:40
Writing library                  : Apple QuickTime
com.apple.finalcutstudio.media.u : 7ABA86C3-580D-4364-970F-5C8103CC66C9

Video
ID                               : 1
Format                           : AVC
Format/Info                      : Advanced Video Codec
Format profile                   : Main@L4.0
Format settings, CABAC           : No
Format settings, ReFrames        : 2 frames
Codec ID                         : vp6
Codec ID/Info                    : Advanced Video Coding
Duration                         : 2mn 25s
Bit rate mode                    : Variable
Bit rate                         : 721 Kbps
Width                            : 1 280 pixels
Height                           : 720 pixels
Display aspect ratio             : 16:9
Frame rate mode                  : Constant
Frame rate                       : 29.970 fps
Resolution                       : 24 bits
Colorimetry                      : 4:2:0
Scan type                        : Progressive
Bits/(Pixel*Frame)               : 0.060
Stream size                      : 64.4 MiB (97%)
Encoded date                     : UTC 2009-08-21 18:45:37
Tagged date                      : UTC 2009-08-21 18:45:40

Audio
ID                               : 3
Format                           : AAC
Format/Info                      : Advanced Audio Codec
Format version                   : Version 4
Format profile                   : LC
Format settings, SBR             : No
Codec ID                         : 40
Duration                         : 2mn 25s
Bit rate mode                    : Constant
Bit rate                         : 124 Kbps
Nominal bit rate                 : 128 Kbps
Channel(s)                       : 2 channels
Channel positions                : L R
Sampling rate                    : 48.0 KHz
Resolution                       : 16 bits
Stream size                      : 2.15 MiB (3%)
Encoded date                     : UTC 2009-08-21 18:45:38
Tagged date                      : UTC 2009-08-21 18:45:40

Menu
ID                               : 2
Encoded_Date                     : UTC 2009-08-21 18:45:37
Tagged_Date                      : UTC 2009-08-21 18:45:40
BitRate_Mode                     : CBR

";

			
		return ($mediaInfoSample);
	}

}

