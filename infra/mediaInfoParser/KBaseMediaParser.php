<?php
/**
 * @package infra
 * @subpackage Media
 */
abstract class KBaseMediaParser
{
	const MEDIA_PARSER_TYPE_MEDIAINFO = '0';
	const MEDIA_PARSER_TYPE_FFMPEG = '1';
	
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
		switch($type)
		{
			case self::MEDIA_PARSER_TYPE_MEDIAINFO:
				return new KMediaInfoMediaParser($filePath, $taskConfig->params->mediaInfoCmd);
				
			case self::MEDIA_PARSER_TYPE_FFMPEG:
				return new KFFMpegMediaParser($filePath, $taskConfig->params->FFMpegCmd);
				
			default:
				return KalturaPluginManager::loadObject('KBaseMediaParser', $type, array($filePath, $taskConfig));
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
			
		return $output;
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