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
			
		$search = array('/', '\\');
		$replace = array(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR);
		$this->srcPath = str_replace($search, $replace ,$srcPath);
		$this->targetPath = str_replace($search, $replace ,$targetPath);
	}
	
	public function crop($quality, $cropType, $width = 0, $height = 0, $cropX = 0, $cropY = 0, $cropWidth = 0, $cropHeight = 0, $scaleWidth = 1, $scaleHeight = 1, $bgcolor = 0xffffff)
	{
		if(is_null($quality))
			$quality = 100;
		if(is_null($cropType))
			$cropType = 1;
		if(is_null($width))
			$width = 0;
		if(is_null($height))
			$height = 0;
		if(is_null($cropX))
			$cropX = 0;
		if(is_null($cropY))
			$cropY = 0;
		if(is_null($cropWidth))
			$cropWidth = 0;
		if(is_null($cropHeight))
			$cropHeight = 0;
		if(is_null($scaleWidth))
			$scaleWidth = 1;
		if(is_null($scaleHeight))
			$scaleHeight = 1;
		if(is_null($bgcolor))
			$bgcolor = 0;
		
		$cmd = $this->getCommand($quality, $cropType, $width, $height, $cropX, $cropY, $cropWidth, $cropHeight, $scaleWidth, $scaleHeight, $bgcolor);
		if($cmd)
		{
			KalturaLog::info("Executing: $cmd");
			$returnValue = null;
			$output = system($cmd, $returnValue);
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
	protected abstract function getCommand($quality, $cropType, $width = 1, $height = 1, $cropX = 0, $cropY = 0, $cropWidth = 0, $cropHeight = 0, $scaleWidth = 0, $scaleHeight = 0, $bgcolor = 0xffffff);
}