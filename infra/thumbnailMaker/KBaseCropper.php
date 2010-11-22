<?php
abstract class KBaseCropper
{
	/**
	 * @var string
	 */
	protected $srcPath;
	
	/**
	 * @var string
	 */
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
	
	public function crop($quality, $cropType, $scaleWidth = 1, $scaleHeight = 1, $cropX = 0, $cropY = 0, $cropWidth = 0, $cropHeight = 0, $bgcolor = 0xffffff)
	{
		$cmd = $this->getCommand($quality, $cropType, $scaleWidth, $scaleHeight, $cropX, $cropY, $cropWidth, $cropHeight, $bgcolor);
		if($cmd)
		{
			KalturaLog::info("Executing: $cmd");
			$returnValue = null;
			$output = system( $cmd , $returnValue );
			KalturaLog::debug("Returned value: '$returnValue'");
			
			if($returnValue)
				return false;
				
			return true;
		}
		
		KalturaLog::info("No conversion required, copying source[$this->srcPath] to target[$this->targetPath]");
		return copy($this->srcPath, $this->targetPath);
	}
	
	/**
	 * @return string
	 */
	protected abstract function getCommand($quality, $cropType, $scaleWidth = 1, $scaleHeight = 1, $cropX = 0, $cropY = 0, $cropWidth = 0, $cropHeight = 0, $bgcolor = 0xffffff);
}