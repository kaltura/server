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
	
	public function createThumnail($position = null, $width = null, $height = null, $params = array())
	{
		$params = self::normalizeParams($params);
		
		KalturaLog::debug("position[$position], width[$width], height[$height], params[".serialize($params)."]");
		$cmd = $this->getCommand($position, $width, $height, $params);
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
	
	protected static function normalizeParams($params = array())
	{
		if(!array_key_exists('frameCount', $params)){
			$params['frameCount'] = 1; 
		}
		
		if(!array_key_exists ('targetType', $params)){
			$params['targetType'] = "image2"; 
		}
		
		if(!array_key_exists ('dar', $params)){
			$params['dar'] = null;
		}
		
		if(!array_key_exists ('vidDur', $params)){
			$params['vidDur'] = null;
		}
		
		if(!array_key_exists ('scanType', $params)){
			$params['scanType'] = null;
		}
		
		return $params;
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
