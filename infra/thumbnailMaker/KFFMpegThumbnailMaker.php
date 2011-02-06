<?php
/**
 * @package infra
 * @subpackage Media
 */
class KFFMpegThumbnailMaker extends KBaseThumbnailMaker
{
	protected $cmdPath;
	
	/**
	 * @param string $filePath
	 * @param string $cmdPath
	 */
	public function __construct($srcPath, $targetPath, $cmdPath = 'ffmpeg')
	{
		$this->cmdPath = $cmdPath;
		parent::__construct($srcPath, $targetPath);
	}
	
	protected function getCommand($position = null, $width = null, $height = null, $frameCount = 1, $targetType = "image2")
	{
		$dimensions = (is_null($width) || is_null($height)) ? '' : ("-s ". $width ."x" . $height);
		$position_str = $position ? " -ss $position " : '';
		$position_str_suffix = $position ? " -ss 0.01 " : "";
		return "$this->cmdPath $position_str -i $this->srcPath -an -y -r 1 $dimensions -vframes $frameCount -f $targetType $position_str_suffix" .
			" $this->targetPath >> $this->targetPath.log 2>&1";
	}
	
	protected function parseOutput($output)
	{
		throw new Exception("Not implemented yet");
	}
}