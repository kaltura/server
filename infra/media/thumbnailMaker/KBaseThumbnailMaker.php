<?php
/**
 * @package server-infra
 * @subpackage Media
 */
abstract class KBaseThumbnailMaker
{
	/**
	 * @var string
	 */
	protected $srcPath;
	protected $targetPath;
	
	/**
	 * @param string $srcPath
	 * @param string $targetPath
	 */
	public function __construct($srcPath, $targetPath)
	{
		if (!file_exists($srcPath))
			throw new Exception("File not found at [$srcPath]");
			
		$this->srcPath = $srcPath;
		$this->targetPath = $targetPath;
	}
	
	public function createThumnail($position = null, $width = null, $height = null, $frameCount = 1, $targetType = "image2", $dar = null)
	{
		KalturaLog::debug("position[$position], width[$width], height[$height], frameCount[$frameCount], frameCount[$frameCount], dar[$dar]");
		$cmd = $this->getCommand($position, $width, $height, $frameCount, $targetType);
		KalturaLog::info("Executing: $cmd");
		
		$returnValue = null;
		$output = system( $cmd , $returnValue );
		KalturaLog::debug("Returned value: '$returnValue'");
		
		if($returnValue)
			return false;
			
		if($this->parseOutput($output)!=true)
			return false;
		
		return true;
	}
	
	/**
	 * @return string
	 */
	protected abstract function getCommand();

	/**
	 * @return int
	 */
	protected abstract function parseOutput($output);
}