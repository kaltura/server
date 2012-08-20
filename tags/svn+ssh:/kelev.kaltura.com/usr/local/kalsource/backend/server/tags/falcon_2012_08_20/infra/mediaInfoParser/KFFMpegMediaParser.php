<?php
/**
 * @package infra
 * @subpackage Media
 */
class KFFMpegMediaParser extends KBaseMediaParser
{
	protected $cmdPath;
	
	/**
	 * @param string $filePath
	 * @param string $cmdPath
	 */
	public function __construct($filePath, $cmdPath = 'ffmpeg')
	{
		$this->cmdPath = $cmdPath;
		parent::__construct($filePath);
	}
	
	protected function getCommand()
	{
		return "{$this->cmdPath} -i {$this->filePath}";
	}
	
	protected function parseOutput($output)
	{
		throw new Exception("Not implemented yet");
	}
}